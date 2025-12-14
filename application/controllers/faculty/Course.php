<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Course extends CI_Controller
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

        // Allow super admin access via /admin without faculty session
        if ($this->url === 'admin') {
            $this->college = $this->db_model->get_row(TABLE_COLLEGE, ['id' => SINGLE_COLLEGE_ID]);
            $this->session_data = [
                'id' => 0,
                'designation' => DESIGNATION_PRINCIPAL,
                'college_id' => $this->college['id'] ?? SINGLE_COLLEGE_ID
            ];
            $this->permissions = ['read' => 'all', 'modify' => 'all'];
        } else {
            $this->common->check_user_session($this->url);
            $this->college = $this->common->get_default_college();
            $this->session_data = $this->session->userdata($this->url);
            $this->permissions = $this->common->get_access_permissions(
                $this->session_data
            );
        }
        // if ( $this->session_data[ 'designation' ] != DESIGNATION_STAFF ) {
        //     $this->common->redirect_route( $this->session_data[ 'designation' ], $this->url );
        // }
    }


    private function check_permisson ($id){


        // $conditions = [
        //     "college_id" => $this->college[ 'id' ],
        //     'is_active' => 1,
        // ];
        $this->db->select('c.id');
        $this->db->from('courses as c');
        $this->db->join('special_courses as sc', 'sc.course_id = c.id AND sc.is_active = 1', 'left');
        $this->db->group_start();
        $this->db->where('c.college_id', $this->college['id']);
        $this->db->or_where('sc.to_college_id', $this->college['id']); // ✅ include shared
        $this->db->group_end();
        $this->db->where('c.id', $id);
        $this->db->where('c.is_active', 1);

       if ($this->permissions["read"] !== "all") {
            if (is_array($this->permissions["read"])) {
                $this->db->where_in("created_by", $this->permissions["read"]);
            } else {
                $conditions["created_by"] = $this->permissions["read"];
            }
        }

        // $courses = $this->db_model->get_all(
        //     'courses', 
        //     $conditions 
        // );
        $courses = $this->db->get()->result_array();
        $courses_ids = array_column($courses,"id");


        return in_array($id,$courses_ids);


    }


    public function index()
    {
        $data["url"] = $this->url;
        $class["classname"] = "courses";
        $class["url"] = $this->url;
        $map = [
            DESIGNATION_STAFF => "staff",
            DESIGNATION_PRINCIPAL => "principal",
        ];
        $path = $map[$this->session_data["designation"]] ?? "hod";
        $class["sidebar_href"] = base_url($this->url . "/" . $path);
        $data["college_id"] = $this->college["id"];
        $data["department"] = $this->session_data["department"];
        $data["title"] = "Add Course";

        $conditions = ["is_active" => 1, "college_id" => $this->college["id"]];

        if ($this->permissions["read"] !== "all") {
            $conditions["created_by"] = $this->permissions["read"];
        }

        $student_conditions = [
            "is_active" => 1,
            "college_id" => $this->college["id"],
        ];

        if ($this->permissions["read"] !== "all") {

           if (
                is_array($this->permissions["department"]) &&
                count($this->permissions["department"]) > 0
            ) {
                $student_conditions["department"] = 
                    $this->permissions["department"];
            }
        }

        $data["student_groups"] = $this->db_model->get_all(
            TABLE_GROUPS,
            $conditions
        );
        $c_types = unserialize(COURSE_TYPES);
        $data['course_types']= $c_types;

        $c_modes = unserialize(COURSE_MODES);
        $data['course_modes'] = $c_modes;
        $department = $this->input->get('department');
        $batch = $this->input->get('batch');

        // $student_conditions = ["is_active"=>true,"college_id"=>$this->college['id']];

        if(!empty($department)){
            $student_conditions['department'] = $department;
        }
        if(!empty($batch)){
            $student_conditions['batch'] = $batch;
        }

        $data["students"] = $this->db_model->get_all(TABLE_STUDENT, $student_conditions);
         

        foreach ($data["students"] as $key => $value) {
            $department = $this->db_model->get_row(TABLE_DEPARTMENT,["id"=>$value['department'],'college_id' => $this->college['id']]);
            $data["students"][$key]['department'] = $department ? ($department['name'] ?? 'N/A') : 'N/A';
        }

        

        

        $data["studentIds"] = [];
        $data["groupIds"] = [];

        $tags_result = $this->db_model->get_all(TABLE_COURCES, ['is_active' => 1, 'college_id' => $this->college['id']]);
        $all_tags = [];
        
        foreach ($tags_result as $row) {
            if (!empty($row['tag'])) {
                $tags_array = explode(',', $row['tag']);
                $all_tags = array_merge($all_tags, $tags_array);
            }
        }
        
        $data['tags'] = array_values(array_unique(array_filter($all_tags)));

        
        $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, [
            "is_active" => 1,
            "college_id" => $this->college["id"],
        ]);

        
        
        $this->load->view("faculty/faculty/sidebar", $class);
        $this->load->view("course/add", $data);
        // $this->load->view("course/assign_students",$data);
        $this->load->view("faculty/faculty/footer");
    }


    // allcourses

    public function allcourses()
    {
        $data["url"] = $this->url;
        $class["classname"] = "allcourses";
        $class["url"] = $this->url;
        $class["college_id"] = $this->college["id"];
        $map = [
            DESIGNATION_STAFF => "staff",
            DESIGNATION_PRINCIPAL => "principal",
        ];
        $path = $map[$this->session_data["designation"]] ?? "hod";
        $class["sidebar_href"] = base_url($this->url . "/" . $path);

        $tags = $this->input->get('tag');

      
        $conditions = [
            "is_active" => true,
            "college_id" => $this->college["id"],
        ];

        if ( !empty( $tags ) ) {
            $conditions[ 'tag LIKE' ] = '%' . $tags . '%';
        }


        $data["cources"] = $this->db_model->get_all(TABLE_COURCES, $conditions);
        $special_conditions = [
            'sc.is_active' => 1,
            'sc.to_college_id' => $this->college['id'],
        ];

        $joins = [
            TABLE_COURCES . ' as c' => 'c.id = sc.course_id AND c.is_active = 1',
        ];
        // $select = [

        // ];
        $data['special_courses'] = $this->db_model->get_with_joins(TABLE_SPECIAL_COURSES.' as sc', $joins, $special_conditions);

        $data["faculty"] = $this->db_model->get_row(TABLE_COURCES, [
            "is_active" => true,
            "college_id" => $this->college["id"],
        ]);

        $tags_result = $this->db_model->get_all(TABLE_COURCES, ['is_active' => 1, 'college_id' => $this->college['id']]);
        $all_tags = [];
        
        foreach ($tags_result as $row) {
            if (!empty($row['tag'])) {
                $tags_array = explode(',', $row['tag']);
                $all_tags = array_merge($all_tags, $tags_array);
            }
        }
        // special courses tags
        // Tag extraction from special_courses (via join with courses)
        $special_tags = [];

        foreach ($data['special_courses'] as $row) {
        if (!empty($row['tag'])) {
            $tags_array = explode(',', $row['tag']);
            $special_tags = array_merge($special_tags, $tags_array);
        }
        }

        $data['sp_tags'] = array_values(array_unique(array_filter($special_tags)));

        
        $data['tags'] = array_values(array_unique(array_filter($all_tags)));

        $this->load->view("faculty/faculty/sidebar", $class);
        $this->load->view("faculty/allcourses", $data);
        $this->load->view("faculty/faculty/footer");
    }


    public function courses()
    {
        $data["url"] = $this->url;
        $class["classname"] = "courses";
        $class["url"] = $this->url;
        $class["college_id"] = $this->college["id"];
        $map = [
            DESIGNATION_STAFF => "staff",
            DESIGNATION_PRINCIPAL => "principal",
        ];
        $path = $map[$this->session_data["designation"]] ?? "hod";
        $class["sidebar_href"] = base_url($this->url . "/" . $path);
       

        $tags = $this->input->get('tag');

      
        $conditions = [
            "is_active" => true,
            "college_id" => $this->college["id"],
            "created_by" => $this->session_data["id"]
        ];


        if ( !empty( $tags ) ) {
            $conditions[ 'tag LIKE' ] = '%' . $tags . '%';
        }

       
    
        // if ($this->permissions["read"] !== "all") {
        //     if (is_array($this->permissions["read"])) {
        //         $this->db->where_in("created_by", $this->permissions["read"]);
        //     } else {
        //         $conditions["created_by"] = $this->permissions["read"];
        //     }

        //     // if ( is_array( $this->permissions[ 'additional_departments' ] ) && count( $this->permissions[ 'additional_departments' ] ) > 0 ) {

        //     //     $conditions[ 'department' ] = $this->permissions[ 'additional_departments' ];
        //     // } else if ( is_array( $this->permissions[ 'department' ] ) && count( $this->permissions[ 'department' ] ) > 0 ) {
        //     //     $conditions[ 'department' ] = $this->permissions[ 'department' ];
        //     // }
        // }

        $data["cources"] = $this->db_model->get_all(TABLE_COURCES, $conditions);
      
        $data["faculty"] = $this->db_model->get_row(TABLE_COURCES, [
            "is_active" => true,
            "college_id" => $this->college["id"],
        ]);

        $tags_result = $this->db_model->get_all(TABLE_COURCES, ['is_active' => 1, 'college_id' => $this->college['id']]);
        // print_r($tags_result);
        // exit;
        $all_tags = [];
        
        foreach ($tags_result as $row) {
            if (!empty($row['tag'])) {
                $tags_array = explode(',', $row['tag']);
                $all_tags = array_merge($all_tags, $tags_array);
            }
        }
        
        $data['tags'] = array_values(array_unique(array_filter($all_tags)));

        $this->load->view("faculty/faculty/sidebar", $class);
        $this->load->view("faculty/cources", $data);
        $this->load->view("faculty/faculty/footer");
    }

    public function add()
    {
        $post = $this->input->post();
        

        if ($post) {
        $courseType = $post['course_type'];
        $typesData = unserialize(COURSE_TYPES);
        $allowedTypes = array_column($typesData, 'id');
        $courseMode = $post['course_mode'];
        $modesData = unserialize(COURSE_MODES);
        $allowedModes = array_column($modesData, 'id');
   
    if(!in_array((int)$courseType, $allowedTypes)) {
    $this->session->set_flashdata("message", [
        "danger",
        "Invalid course type selected.",
    ]);
    return redirect(base_url($this->url . "/course"));
    }

    if(!in_array((int)$courseMode, $allowedModes)) {
    $this->session->set_flashdata("message", [
        "danger",
        "Invalid course mode selected.",
    ]);
    return redirect(base_url($this->url . "/course"));
    }
            $this->form_validation->set_rules(
                "course_code",
                "Cource ID",
                "trim|required|min_length[3]|max_length[255]"
            );
            $this->form_validation->set_rules(
                "course_name",
                "Cource Name",
                "trim|required|max_length[150]"
            );
            $this->form_validation->set_rules(
                "description",
                "Description",
                "trim|required|max_length[500]"
            );

            if ($this->form_validation->run() == false) {
                $this->session->set_flashdata("message", [
                    "danger",
                    validation_errors(),
                ]);
                return redirect(base_url($this->url . "/course"));
            } else {

                $course_name = trim($this->input->post("course_name"));

                // Check if course with same name already exists
                $existing_course = $this->db_model->get_row(TABLE_COURCES, [
                    'name' => $course_name,
                    'college_id' => $this->college['id'],
                    'is_active' => 1
                ]);

                if ($existing_course) {
                    $course_id = $existing_course['id'];
                    $this->session->set_flashdata('message', array('info', "Course '{$course_name}' already exists. Using existing course."));
                } else {
                    $tags = implode(',', array_unique(array_filter($this->input->post("tags"))));
                    $data = [
                        "course_code" => $this->input->post("course_code"),
                        "name" => $course_name,
                        "description" => $this->input->post("description"),
                        "created_by" => $this->session_data["id"],
                        "college_id" => $this->college["id"],
                        "attempts" => $this->input->post("attempts"),
                        "start_date" => $this->input->post("start_date"),
                        "end_date" => $this->input->post("end_date"),
                        "tag" =>  $tags,
                        "explanation_start_date" => $this->input->post("explanation_start_date"),
                        "explanation_end_date" => $this->input->post("explanation_end_date"),
                        "show_explanation" => $this->input->post("show_explanation") == "on" ? 1 : 0,
                        "course_type" => $courseType,
                        "course_mode" => $courseMode
                    ];
                        $this->session->set_flashdata('message', array('success', "Course Created successfully!"));
                    } else {
                        $this->session->set_flashdata('message', array('danger', "Failed to create Course."));
                        return redirect(base_url($this->url . "/course"));
                    }
                }

                // Handle enrollments for both existing and new courses
                if (isset($course_id)) {
                    $students = $this->input->post("students");
                    $student_groups = $this->input->post("student_groups");
                    $departments = $this->input->post("department");

                    if (!empty($students)) {
                        foreach ($students as $student) {
                            $data = [
                                "course_id" => $course_id,
                                "student_id" => $student,
                            ];
                            $this->db_model->insert(
                                TABLE_COURSE_STUDENTS,
                                $data
                            );
                        }
                    }

                    if (!empty($student_groups)) {
                        foreach ($student_groups as $group) {
                            $data = [
                                "course_id" => $course_id,
                                "group_id" => $group,
                            ];
                            $this->db_model->insert(TABLE_COURSE_GROUPS, $data);
                        }
                    }

                    if (!empty($departments)) {
                        foreach ($departments as $department) {
                            $data = [
                                "course_id" => $course_id,
                                "department_id" => $department,
                                "college_id" => $this->college["id"],
                                "created_by" => $this->session_data["id"],
                            ];
                            $this->db_model->insert(
                                TABLE_COURSE_DEPARTMENTS,
                                $data
                            );
                        }
                    }
                }
                redirect(base_url($this->url . "/course"));
            }
        } else {
            redirect(base_url($this->url . "/course"));
        }
    }

    public function edit($college_id = null, $id = null)
    {

        if(!$this->check_permisson($id)){

            redirect( base_url( $this->url . '/course' ) );

        }

        $post = $this->input->post();
        
        if ($post) {
        $courseType = $post['course_type'];
        $typesData = unserialize(COURSE_TYPES);
        $allowedTypes = array_column($typesData,'id');
        $courseMode = $post['course_mode'];
        $modesData = unserialize(COURSE_MODES);
        $allowedModes = array_column($modesData,'id');

         if(!in_array((int)$courseType, $allowedTypes)) {
            $this->session->set_flashdata("message", [
                "danger",
                "Invalid course type selected.",
            ]);
            return redirect(base_url($this->url . "/course"));
            }

          if(!in_array((int)$courseMode, $allowedModes)) {
            $this->session->set_flashdata("message", [
                "danger",
                "Invalid course mode selected.",
            ]);
            return redirect(base_url($this->url . "/course"));
            }

            $this->form_validation->set_rules(
                "course_code",
                "Cource ID",
                "trim|required|min_length[3]|max_length[255]"
            );
            $this->form_validation->set_rules(
                "course_name",
                "Cource Name",
                "trim|required|max_length[150]"
            );
            $this->form_validation->set_rules(
                "description",
                "Description",
                "trim|required|max_length[500]"
            );


            if ($this->form_validation->run() == false) {
                $this->session->set_flashdata("message", [
                    "danger",
                    validation_errors(),
                ]);
                return redirect(base_url($this->url . "/course"));
            } else {
                $tags = implode(',', array_unique(array_filter($this->input->post("tags"))));

                $data = [
                    "course_code" => $this->input->post("course_code"),
                    "name" => $this->input->post("course_name"),
                    "description" => $this->input->post("description"),
                    "start_date" => $this->input->post("start_date"),
                    "end_date" => $this->input->post("end_date"),
                    "attempts" => $this->input->post("attempts"),
                    "tag" => $tags,
                    "college_id" => $college_id,
                    "explanation_start_date" => $this->input->post("explanation_start_date"),
                    "explanation_end_date" => $this->input->post("explanation_end_date"),
                    "show_explanation" => $this->input->post("show_explanation") == "on" ? 1 : 0,
                    "course_type" => $courseType,
                    "course_mode" => $courseMode
                ];
                if (
                    $this->db_model->update(TABLE_COURCES, $data, [
                        "id" => $id,
                        "college_id" => $college_id,
                        "is_active" => 1,
                        "created_by" => $this->session_data["id"],
                    ])
                ) {
                    // $this->db_model->delete(TABLE_COURSE_STUDENTS, [
                    //     "course_id" => $id,
                    // ]);
                    // $this->db_model->delete(TABLE_COURSE_GROUPS, [
                    //     "course_id" => $id,
                    // ]);
                    // $this->db_model->delete(TABLE_COURSE_DEPARTMENTS, [
                    //     "course_id" => $id,
                    // ]);
                    
                    // Delete direct students
                    $this->db->query("
                        DELETE cs
                        FROM " . TABLE_COURSE_STUDENTS . " cs
                        JOIN " . TABLE_STUDENT . " s ON s.id = cs.student_id
                        WHERE cs.course_id = ?
                        AND cs.is_active = 1
                        AND s.is_active = 1
                        AND s.college_id = ?
                    ", [$id, $this->college["id"]]);

                    // Delete group students
                    $this->db->query("
                        DELETE cg
                        FROM " . TABLE_COURSE_GROUPS . " cg
                        JOIN " . TABLE_GROUPS . " g ON g.id = cg.group_id
                        JOIN " . TABLE_MEMGROUPS . " gm ON gm.group_id = g.id
                        JOIN " . TABLE_STUDENT . " s ON s.id = gm.student_id
                        WHERE cg.course_id = ?
                        AND cg.is_active = 1
                        AND s.is_active = 1
                        AND s.college_id = ?
                    ", [$id, $this->college["id"]]);

                    // Delete department students
                    $this->db->query("
                        DELETE cd
                        FROM " . TABLE_COURSE_DEPARTMENTS . " cd
                        JOIN " . TABLE_DEPARTMENT . " d ON d.id = cd.department_id
                        JOIN " . TABLE_STUDENT . " s ON s.department = d.id
                        WHERE cd.course_id = ?
                        AND s.is_active = 1
                        AND s.college_id = ?
                    ", [$id, $this->college["id"]]);

                    $students = $this->input->post("students");
                    $student_groups = $this->input->post("student_groups");

                    if (!empty($students)) {
                        foreach ($students as $student) {
                            $data = [
                                "course_id" => $id,
                                "student_id" => $student,
                            ];
                            $this->db_model->insert(
                                TABLE_COURSE_STUDENTS,
                                $data
                            );
                        }
                    }

                    if (!empty($student_groups)) {
                        foreach ($student_groups as $group) {
                            $data = [
                                "course_id" => $id,
                                "group_id" => $group,
                            ];
                            $this->db_model->insert(TABLE_COURSE_GROUPS, $data);
                        }
                    }

                    $departments = $this->input->post("department");

                    if (!empty($departments)) {
                        foreach ($departments as $department) {
                            $data = [
                                "course_id" => $id,
                                "department_id" => $department,
                                "college_id" => $this->college["id"],
                                "created_by" => $this->session_data["id"],
                            ];
                            $this->db_model->insert(
                                TABLE_COURSE_DEPARTMENTS,
                                $data
                            );
                        }
                    }

                    $this->session->set_flashdata("message", [
                        "success",
                        "Cource updated successfully!",
                    ]);
                } else {
                    $this->session->set_flashdata("message", [
                        "danger",
                        "Failed to update the cource.",
                    ]);
                }
                redirect(base_url($this->url . "/course"));
            }
        } else {
            $data["college_id"] = $college_id;

            $data["course"] = $this->db_model->get_row(TABLE_COURCES, [
                "is_active" => 1,
                "college_id" => $college_id,
                "id" => $id
            ]);
            $data["course_types"] = unserialize(COURSE_TYPES);
            $data["course_modes"] = unserialize(COURSE_MODES);
            $data["url"] = $this->url;
            $data["department"] = $this->session_data["department"];
            $class["classname"] = "cources";
            $data["title"] = "Edit Cource";
            $class["url"] = $this->url;
            $class["sidebar_href"] = base_url($this->url . "/course");
            $conditions = [
                "is_active" => 1,
                "college_id" => $this->college["id"],
            ];

            if ($this->permissions["read"] !== "all") {
                $conditions["created_by"] = $this->permissions["read"];
            }

            $student_conditions = [
                "is_active" => 1,
                "college_id" => $this->college["id"],
            ];

            if ($this->permissions["read"] !== "all") {
                // $student_conditions[ 'created_by' ] = $this->permissions[ 'read' ];

                if (
                    is_array($this->permissions["department"]) &&
                    count($this->permissions["department"]) > 0
                ) {
                    $student_conditions["department"] =
                        $this->permissions["department"];
                }
            }

            $data["student_groups"] = $this->db_model->get_all(
                TABLE_GROUPS,
                $conditions
            );
            $data["students"] = $this->db_model->get_all(
                TABLE_STUDENT,
                $student_conditions
            );
            foreach ($data["students"] as $key => $value) {
                $department = $this->db_model->get_row(TABLE_DEPARTMENT,["id"=>$value['department'],'college_id' => $this->college['id']]);
                $data["students"][$key]['department'] = $department ? $department['name'] : 'Unknown';
            }
    
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, [
                "is_active" => 1,
                "college_id" => $this->college["id"],
            ]);
            $data["departmentIds"] = array_column(
                $this->db_model->get_all(TABLE_COURSE_DEPARTMENTS, [
                    "course_id" => $id,
                ]),
                "department_id"
            );
            $data["studentIds"] = array_column(
                $this->db_model->get_all(TABLE_COURSE_STUDENTS, [
                    "course_id" => $id,
                ]),
                "student_id"
            );
            $data["groupIds"] = array_column(
                $this->db_model->get_all(TABLE_COURSE_GROUPS, [
                    "course_id" => $id,
                ]),
                "group_id"
            );

        
            $tags_result = $this->db_model->get_all(TABLE_COURCES, ['is_active' => 1, 'college_id' => $this->college['id']]);
            // print_r($tags_result);
            // exit;
            $all_tags = [];
            
            foreach ($tags_result as $row) {
                if (!empty($row['tag'])) {
                    $tags_array = explode(',', $row['tag']);
                    $all_tags = array_merge($all_tags, $tags_array);
                }
            }
            
            $data['tags'] = array_values(array_unique(array_filter($all_tags)));
            
           

            $this->load->view("faculty/faculty/sidebar", $class);
            $this->load->view("course/add", $data);
          
            $this->load->view("faculty/faculty/footer");
        }
    }



    // special course assign students

    public function assign_students($id = null)
    {
  
        $post = $this->input->post();

        if ($post) {
            try
            {
            // Clear previous assignments first
                // $this->db_model->delete(TABLE_COURSE_STUDENTS, ["course_id" => $id,]);
                // $this->db_model->delete(TABLE_COURSE_GROUPS, ["course_id" => $id,"college_id" => $this->college["id"]]);
                // $this->db_model->delete(TABLE_COURSE_DEPARTMENTS, ["course_id" => $id,"college_id" => $this->college["id"]]);
              
                // Delete direct students
                $this->db->query("
                    DELETE cs
                    FROM " . TABLE_COURSE_STUDENTS . " cs
                    JOIN " . TABLE_STUDENT . " s ON s.id = cs.student_id
                    WHERE cs.course_id = ?
                    AND cs.is_active = 1
                    AND s.is_active = 1
                    AND s.college_id = ?
                ", [$id, $this->college["id"]]);

                // Delete group students
                $this->db->query("
                    DELETE cg
                    FROM " . TABLE_COURSE_GROUPS . " cg
                    JOIN " . TABLE_GROUPS . " g ON g.id = cg.group_id
                    JOIN " . TABLE_MEMGROUPS . " gm ON gm.group_id = g.id
                    JOIN " . TABLE_STUDENT . " s ON s.id = gm.student_id
                    WHERE cg.course_id = ?
                    AND cg.is_active = 1
                    AND s.is_active = 1
                    AND s.college_id = ?
                ", [$id, $this->college["id"]]);

                // Delete department students
                $this->db->query("
                    DELETE cd
                    FROM " . TABLE_COURSE_DEPARTMENTS . " cd
                    JOIN " . TABLE_DEPARTMENT . " d ON d.id = cd.department_id
                    JOIN " . TABLE_STUDENT . " s ON s.department = d.id
                    WHERE cd.course_id = ?
                    AND s.is_active = 1
                    AND s.college_id = ?
                ", [$id, $this->college["id"]]);


                // Assign students
                $students = $this->input->post("students");
                if (!empty($students)) {
                    foreach ($students as $student_id) {
                        $this->db_model->insert(TABLE_COURSE_STUDENTS, [
                            "course_id" => $id,
                            "student_id" => $student_id
                        ]);
                    }
                }

                // Assign groups
                $groups = $this->input->post("student_groups");
                if (!empty($groups)) {
                    foreach ($groups as $group_id) {
                        $this->db_model->insert(TABLE_COURSE_GROUPS, [
                            "course_id" => $id,
                            "group_id" => $group_id
                        ]);
                    }
                }

                // Assign departments
                $departments = $this->input->post("department");
                if (!empty($departments)) {
                    foreach ($departments as $dept_id) {
                        $this->db_model->insert(TABLE_COURSE_DEPARTMENTS, [
                            "course_id" => $id,
                            "department_id" => $dept_id,
                            "college_id" => $this->college["id"],
                            "created_by" => $this->session_data["id"],
                        ]);
                    }
                }
                $this->session->set_flashdata('s_message', ['success', 'Students assigned successfully.']);
            } catch ( Exception $e) {
                // Set error message
                $this->session->set_flashdata('s_message', ['danger', 'Failed to assign students. Please try again.']);
            }
            // Redirect after saving
            redirect(base_url($this->url . "/allcourses"));
        } else {
            // Load course info
            $data["course"] = $this->db_model->get_row(TABLE_COURCES, [
                "id" => $id,
                "is_active" => 1
            ]);

            // Preload assigned IDs
            $data["studentIds"] = array_column($this->db_model->get_all(TABLE_COURSE_STUDENTS, ["course_id" => $id,'is_active'=>1]), "student_id");
            $data["groupIds"] = array_column($this->db_model->get_all(TABLE_COURSE_GROUPS, ["course_id" => $id]), "group_id");
            $data["departmentIds"] = array_column($this->db_model->get_all(TABLE_COURSE_DEPARTMENTS, ["course_id" => $id]), "department_id");

            // Fetch dropdown data
            $conditions = [
                "is_active" => 1,
                "college_id" => $this->college["id"]
            ];

            // Permissions filtering
            // if ($this->permissions["read"] !== "all") {
            //     $conditions["created_by"] = $this->permissions["read"];
            // }

            $student_conditions = $conditions;

            // if (is_array($this->permissions["department"]) && count($this->permissions["department"]) > 0) {
            //     $student_conditions["department"] = $this->permissions["department"];
            // }

            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, $conditions);
            $data["student_groups"] = $this->db_model->get_all(TABLE_GROUPS, $conditions);

            $students = $this->db_model->get_all(TABLE_STUDENT, $student_conditions);
            foreach ($students as $i => $student) {
                $students[$i]['department'] = $this->db_model->get_row(TABLE_DEPARTMENT, [
                    "id" => $student['department'],
                    "college_id" => $this->college['id']
                ])['name'] ?? 'Unknown';
            }
            $data["students"] = $students;

            $data["url"] = $this->url;
            $data["title"] = "Special Course - Assign Students";
            $class["classname"] = "cources";
            $class["url"] = $this->url;
            $class["sidebar_href"] = base_url($this->url . "/allcourses");

            // Load views
            $this->load->view("faculty/faculty/sidebar", $class);
            $this->load->view("course/assign_students", $data);
            $this->load->view("faculty/faculty/footer");
        }
    }


    public function delete($college_id = null, $id = null)
    {

        if(!$this->check_permisson($id)){

            redirect( base_url( $this->url . '/course' ) );

        }


        // check request belongs to authorized college and owner of the record

        $course =   $this->db_model->get_row(
            TABLE_COURCES,
            ["id" => $id, "college_id" => $this->college['id'],'created_by' => $this->session_data["id"] ]
        );

        if (!$course){
            $this->session->set_flashdata("message", [
                "danger",
                "Failed to delete the cource.",
            ]);
            redirect(base_url($this->url . "/course"));
        }


        if (
            $this->db_model->update(
                TABLE_COURCES,
                ["is_active" => 0,'created_by' => $this->session_data["id"]],
                ["id" => $id, "college_id" => $college_id]
            )
        ) {
            $this->session->set_flashdata("message", [
                "success",
                "Cource deleted successfully!",
            ]);
        } else {
            $this->session->set_flashdata("message", [
                "danger",
                "Failed to delete the cource.",
            ]);
        }
        redirect(base_url($this->url . "/course"));
    }

    public function test($course_id = null)
    {

        if(!$this->check_permisson($course_id)){

            redirect( base_url( $this->url . '/course' ) );

        }


        $data["url"] = $this->url;
        $class["classname"] = "courses";
        $class["url"] = $this->url;
        $map = [
            DESIGNATION_STAFF => "staff",
            DESIGNATION_PRINCIPAL => "principal",
        ];
        $path = $map[$this->session_data["designation"]] ?? "hod";
        $class["sidebar_href"] = base_url($this->url . "/" . $path);
        $data["college_id"] = $this->college["id"];

        // For adding tests to a course
        if ($course_id) {
            $data["course"] = $this->db_model->get_row(TABLE_COURCES, [
                "id" => $course_id,
                "is_active" => 1,
                "college_id" => $this->college["id"],
            ]);

            if (!$data["course"]) {
                $this->session->set_flashdata("message", [
                    "danger",
                    "Course not found.",
                ]);
                redirect(base_url($this->url . "/course"));
            }

            // Get course tests
            //
            $data["course_tests"] = $this->db
                ->select("t.*", "tm.name as module")
                ->from("course_tests ct")
                ->join("tests t", "t.id = ct.test_id")
                ->join("test_modules tm", "tm.id = t.module_id", "left")
                ->where("ct.course_id", $course_id)
                ->where("ct.is_active", 1)
                ->where("t.is_active", 1)
                ->get()
                ->result_array();

            // Get available tests
            $test_conditions = [
                "is_active" => 1,
                "college_id" => $this->college["id"],
            ];

            if ($this->permissions["read"] !== "all") {
                $test_conditions["created_by"] = $this->permissions["read"];
            }

            $data["available_tests"] = $this->db_model->get_all(
                "tests",
                $test_conditions
            );
            // Get available tests with modules
            // Get available tests with modules
            // modules for available tests

            // Filter out tests already added to the course
            if (!empty($data["course_tests"])) {
                $course_test_ids = array_column($data["course_tests"], "id");
                foreach ($data["available_tests"] as $key => $test) {
                    if (in_array($test["id"], $course_test_ids)) {
                        unset($data["available_tests"][$key]);
                    }
                }
                $data["available_tests"] = array_values(
                    $data["available_tests"]
                );
            }

            $this->load->view("faculty/faculty/sidebar", $class);
            $this->load->view("course/course_tests", $data);
            $this->load->view("faculty/faculty/footer");
        } else {
            redirect(base_url($this->url . "/course"));
        }
    }

    public function add_test_to_course()
    {
        $post = $this->input->post();
        if ($post) {
            $course_id = $this->input->post("course_id");
            $test_ids = $this->input->post("test_ids");


            if(!$this->check_permisson($course_id)){

                redirect( base_url( $this->url . '/course' ) );
    
            }
    


            if (empty($test_ids)) {
                $this->session->set_flashdata("message", [
                    "danger",
                    "Please select at least one test to add.",
                ]);
                redirect(
                    base_url($this->url . "/course/test/add/" . $course_id)
                );
            }

            $success = true;
            foreach ($test_ids as $test_id) {
                // Check if already exists
                $exists = $this->db_model->get_row("course_tests", [
                    "course_id" => $course_id,
                    "test_id" => $test_id,
                    "is_active" => 1,
                ]);

                if (!$exists) {
                    $data = [
                        "course_id" => $course_id,
                        "test_id" => $test_id,
                        "is_active" => 1,
                    ];

                    if (!$this->db_model->insert("course_tests", $data)) {
                        $success = false;
                    }
                }
            }

            if ($success) {
                $this->session->set_flashdata("message", [
                    "success",
                    "Tests added to course successfully!",
                ]);
            } else {
                $this->session->set_flashdata("message", [
                    "danger",
                    "Failed to add some or all tests to the course.",
                ]);
            }

            redirect(base_url($this->url . "/course/test/add/" . $course_id));
        } else {
            redirect(base_url($this->url . "/course"));
        }
    }

    public function remove_test_from_course($course_id, $test_id)
    {


        if(!$this->check_permisson($course_id)){

            redirect( base_url( $this->url . '/course' ) );

        }


        if (
            $this->db_model->update(
                "course_tests",
                ["is_active" => 0],
                ["course_id" => $course_id, "test_id" => $test_id]
            )
        ) {
            $this->session->set_flashdata("message", [
                "success",
                "Test removed from course successfully!",
            ]);
        } else {
            $this->session->set_flashdata("message", [
                "danger",
                "Failed to remove test from course.",
            ]);
        }

        redirect(base_url($this->url . "/course/test/add/" . $course_id));
    }

    public function view_students($course_id = null)
    {
        // print_r($this->check_permisson($course_id))
        // exit;
        if(!$this->check_permisson($course_id)){

            redirect( base_url( $this->url . '/course' ) );

        }


        if (!$course_id) {
            redirect(base_url($this->url . "/course"));
        }

        $data["url"] = $this->url;
        $class["classname"] = "courses";
        $class["url"] = $this->url;
        $map = [
            DESIGNATION_STAFF => "staff",
            DESIGNATION_PRINCIPAL => "principal",
        ];
        $path = $map[$this->session_data["designation"]] ?? "hod";
        $class["sidebar_href"] = base_url($this->url . "/" . $path);
        $data["college_id"] = $this->college["id"];

        // Get course details
        // $data["course"] = $this->db_model->get_row(TABLE_COURCES, [
        //     "id" => $course_id,
        //     "is_active" => 1,
        //     "college_id" => $this->college["id"],
        // ]);
        // Modified: Fetch course from either direct or shared (special) mapping
    
        $data["course"] = $this->common->get_course_details($course_id, $this->college['id']);

        if (!$data["course"]) {
            $this->session->set_flashdata("message", [
                "danger",
                "Course not found.",
            ]);
            redirect(base_url($this->url . "/course"));
        }

        // Get students directly added to the course
        $direct_students = $this->db
            ->select('s.*, "Direct Addition" as source')
            ->from("course_students cs")
            ->join("students s", "s.id = cs.student_id")
            ->where("cs.course_id", $course_id)
            ->where("cs.is_active", 1)
            ->where("s.is_active", 1)
            ->where("s.college_id", $this->college["id"])
            ->get()
            ->result_array();

        // Get students via groups
        $group_students = $this->db
            ->select('s.*, g.name, CONCAT("Group: ", g.name) as source')
            ->from("course_groups cg")
            ->join("groups g", "g.id = cg.group_id")
            ->join("group_members gm", "gm.group_id = g.id")
            ->join("students s", "s.id = gm.student_id")
            ->where("cg.course_id", $course_id)
            ->where("cg.is_active", 1)
            ->where("s.is_active", 1)
            ->where("s.college_id", $this->college["id"])
            ->get()
            ->result_array();

        // Get students via departments
        $dept_students = $this->db
            ->select('s.*, d.name as department_name, CONCAT("Department: ", d.name) as source')
            ->from("course_departments cd")
            ->join("departments d", "d.id = cd.department_id")
            ->join("students s", "s.department = d.id")
            ->where("cd.course_id", $course_id)
            ->where("s.is_active", 1)
            ->where("s.college_id", $this->college["id"])
            ->get()
            ->result_array();

        // Combine all students and remove duplicates
        $all_students = array_merge(
            $direct_students,
            $group_students,
            $dept_students
        );

        // Remove duplicates based on student ID while preserving the first source found
        $unique_students = [];
        $student_ids = [];

        foreach ($all_students as $student) {
            if (!in_array($student["id"], $student_ids)) {
                $student_ids[] = $student["id"];
                $unique_students[] = $student;
            }
        }

        $data["students"] = $unique_students;

        $this->load->view("faculty/faculty/sidebar", $class);
        $this->load->view("course/view_students", $data);
        $this->load->view("faculty/faculty/footer");
    }

    // Add these methods to your existing Course controller

    public function modules($course_id = null)
    {


    

        if (!$course_id) {
            redirect(base_url($this->url . "/course"));
        }

        if(!$this->check_permisson($course_id)){

            redirect( base_url( $this->url . '/course' ) );

        }


        $data["url"] = $this->url;
        $class["classname"] = "courses";
        $class["url"] = $this->url;
        $map = [
            DESIGNATION_STAFF => "staff",
            DESIGNATION_PRINCIPAL => "principal",
        ];
        $path = $map[$this->session_data["designation"]] ?? "hod";
        $class["sidebar_href"] = base_url($this->url . "/" . $path);
        $data["college_id"] = $this->college["id"];

        // Get course details
        $data["course"] = $this->db_model->get_row(TABLE_COURCES, [
            "id" => $course_id,
            "is_active" => 1,
            "college_id" => $this->college["id"],
        ]);

        if (!$data["course"]) {
            $this->session->set_flashdata("message", [
                "danger",
                "Course not found.",
            ]);
            redirect(base_url($this->url . "/course"));
        }

        // Get course modules
        $data["modules"] = $this->db_model->get_all("course_modules", [
            "course_id" => $course_id,
            "is_active" => 1,
        ]);

        // Get tests for each module
        $data["module_tests"] = [];
        // if (!empty($data["modules"])) {
        //     foreach ($data["modules"] as $module) {
        //         // Get tests for this module
        //         $module_tests = $this->db
        //             ->select("ct.*, ct.id as course_test_id, t.title as name, t.duration,t.instructions as description")
        //             ->from("course_tests ct")
        //             ->join("tests t", "t.id = ct.test_id")
        //             ->where("ct.course_id", $course_id)
        //             ->where("ct.module_id", $module['id'])
        //             ->where("ct.is_active", 1)
        //             ->get()
        //             ->result_array();
                
        //         if (!empty($module_tests)) {
        //             $data["module_tests"][$module['id']] = $module_tests;
        //         }
        //     }
        // }

        $this->load->view("faculty/faculty/sidebar", $class);
        $this->load->view("course/modules", $data);
        $this->load->view("faculty/faculty/footer");
    }

    // allcourses_modules

    public function allcourses_modules($course_id = null)
    {
        if (!$course_id) {
            redirect(base_url($this->url . "/allcourses"));
        }

        $data["url"] = $this->url;
        $class["classname"] = "allcourses";
        $class["url"] = $this->url;
        $map = [
            DESIGNATION_STAFF => "staff",
            DESIGNATION_PRINCIPAL => "principal",
        ];
        $path = $map[$this->session_data["designation"]] ?? "hod";
        $class["sidebar_href"] = base_url($this->url . "/" . $path);
        $data["college_id"] = $this->college["id"];

        // Get course details
        // $data["course"] = $this->db_model->get_row(TABLE_COURCES, [
        //     "id" => $course_id,
        //     "is_active" => 1,
        //     "college_id" => $this->college["id"],
        // ]);
        // Modified: Fetch course from either direct or shared (special) mapping

        $data["course"] = $this->common->get_course_details($course_id, $this->college['id']);;

        if (!$data["course"]) {
            $this->session->set_flashdata("message", [
                "danger",
                "Course not found.",
            ]);
            redirect(base_url($this->url . "/course"));
        }

        // Get course modules
        $data["modules"] = $this->db_model->get_all("course_modules", [
            "course_id" => $course_id,
            "is_active" => 1,
        ]);

        // Get tests for each module
        $data["module_tests"] = [];
        // if (!empty($data["modules"])) {
        //     foreach ($data["modules"] as $module) {
        //         // Get tests for this module
        //         $module_tests = $this->db
        //             ->select("ct.*, ct.id as course_test_id, t.title as name, t.duration,t.instructions as description")
        //             ->from("course_tests ct")
        //             ->join("tests t", "t.id = ct.test_id")
        //             ->where("ct.course_id", $course_id)
        //             ->where("ct.module_id", $module['id'])
        //             ->where("ct.is_active", 1)
        //             ->get()
        //             ->result_array();
                
        //         if (!empty($module_tests)) {
        //             $data["module_tests"][$module['id']] = $module_tests;
        //         }
        //     }
        // }

        $this->load->view("faculty/faculty/sidebar", $class);
        $this->load->view("course/all_course_modules", $data);
        $this->load->view("faculty/faculty/footer");
    }
    

    public function add_module()
    {
        $post = $this->input->post();
        if ($post) {
            $course_id = $this->input->post("course_id");
            $module_name = $this->input->post("module_name");

            if(!$this->check_permisson($course_id)){

                redirect( base_url( $this->url . '/course' ) );
    
            }
    

            if (empty($module_name)) {
                $this->session->set_flashdata("message", [
                    "danger",
                    "Module name is required.",
                ]);
                redirect(base_url($this->url . "/course/modules/" . $course_id));
            }

            // Insert module
            $data = [
                "name" => $module_name,
                "course_id" => $course_id,
                "college_id" => $this->college["id"],
                "is_active" => 1,
            ];

            if ($this->db_model->insert("course_modules", $data)) {
                $this->session->set_flashdata("message", [
                    "success",
                    "Module added successfully!",
                ]);
            } else {
                $this->session->set_flashdata("message", [
                    "danger",
                    "Failed to add module.",
                ]);
            }

            redirect(base_url($this->url . "/course/modules/" . $course_id));
        } else {
            redirect(base_url($this->url . "/course"));
        }
    }

    public function edit_module($course_id = null, $module_id = null)
    {
        if (!$course_id || !$module_id) {
            redirect(base_url($this->url . "/course"));
        }

        if(!$this->check_permisson($course_id)){

            redirect( base_url( $this->url . '/course' ) );

        }


        $post = $this->input->post();
        if ($post) {
            $module_name = $this->input->post("module_name");

            if (empty($module_name)) {
                $this->session->set_flashdata("message", [
                    "danger",
                    "Module name is required.",
                ]);
                redirect(base_url($this->url . "/course/edit_module/" . $course_id . "/" . $module_id));
            }

            // Update module
            $data = [
                "name" => $module_name,
            ];

            if ($this->db_model->update("course_modules", $data, [
                "id" => $module_id,
                "course_id" => $course_id,
                "is_active" => 1,
            ])) {
                $this->session->set_flashdata("message", [
                    "success",
                    "Module updated successfully!",
                ]);
                redirect(base_url($this->url . "/course/modules/" . $course_id));
            } else {
                $this->session->set_flashdata("message", [
                    "danger",
                    "Failed to update module.",
                ]);
                redirect(base_url($this->url . "/course/edit_module/" . $course_id . "/" . $module_id));
            }
        } else {
            $data["url"] = $this->url;
            $class["classname"] = "courses";
            $class["url"] = $this->url;
            $map = [
                DESIGNATION_STAFF => "staff",
                DESIGNATION_PRINCIPAL => "principal",
            ];
            $path = $map[$this->session_data["designation"]] ?? "hod";
            $class["sidebar_href"] = base_url($this->url . "/" . $path);
            $data["college_id"] = $this->college["id"];

            // Get course details
            $data["course"] = $this->db_model->get_row(TABLE_COURCES, [
                "id" => $course_id,
                "is_active" => 1,
                "college_id" => $this->college["id"],
            ]);

            if (!$data["course"]) {
                $this->session->set_flashdata("message", [
                    "danger",
                    "Course not found.",
                ]);
                redirect(base_url($this->url . "/course"));
            }

            // Get module details
            $data["module"] = $this->db_model->get_row("course_modules", [
                "id" => $module_id,
                "course_id" => $course_id,
                "is_active" => 1,
            ]);

            if (!$data["module"]) {
                $this->session->set_flashdata("message", [
                    "danger",
                    "Module not found.",
                ]);
                redirect(base_url($this->url . "/course/modules/" . $course_id));
            }

            $this->load->view("faculty/faculty/sidebar", $class);
            $this->load->view("course/edit_module", $data);
            $this->load->view("faculty/faculty/footer");
        }
    }

    public function delete_module($course_id = null, $module_id = null)
    {
        if (!$course_id || !$module_id) {
            redirect(base_url($this->url . "/course"));
        }

        if(!$this->check_permisson($course_id)){

            redirect( base_url( $this->url . '/course' ) );

        }


        // Delete module (set is_active to 0)
        if ($this->db_model->update("course_modules", ["is_active" => 0], [
            "id" => $module_id,
            "course_id" => $course_id,
        ])) {
            // Also deactivate all tests associated with this module
            $this->db_model->update("course_tests", ["is_active" => 0], [
                "course_id" => $course_id,
                "module_id" => $module_id,
            ]);

            $this->session->set_flashdata("message", [
                "success",
                "Module deleted successfully!",
            ]);
        } else {
            $this->session->set_flashdata("message", [
                "danger",
                "Failed to delete module.",
            ]);
        }

        redirect(base_url($this->url . "/course/modules/" . $course_id));
    }

    public function module_tests($course_id = null, $module_id = null)
    {
        if (!$course_id || !$module_id) {
            redirect(base_url($this->url . "/course"));
        }

        if(!$this->check_permisson($course_id)){

            redirect( base_url( $this->url . '/course' ) );

        }


        $data["url"] = $this->url;
        $class["classname"] = "courses";
        $class["url"] = $this->url;
        $map = [
            DESIGNATION_STAFF => "staff",
            DESIGNATION_PRINCIPAL => "principal",
        ];
        $path = $map[$this->session_data["designation"]] ?? "hod";
        $class["sidebar_href"] = base_url($this->url . "/" . $path);
        $data["college_id"] = $this->college["id"];

        // Get course details
        $data["course"] = $this->db_model->get_row(TABLE_COURCES, [
            "id" => $course_id,
            "is_active" => 1,
            "college_id" => $this->college["id"],
        ]);

        if (!$data["course"]) {
            $this->session->set_flashdata("message", [
                "danger",
                "Course not found.",
            ]);
            redirect(base_url($this->url . "/course"));
        }

        // Get module details
        $data["module"] = $this->db_model->get_row("course_modules", [
            "id" => $module_id,
            "course_id" => $course_id,
            "is_active" => 1,
        ]);

        if (!$data["module"]) {
            $this->session->set_flashdata("message", [
                "danger",
                "Module not found.",
            ]);
            redirect(base_url($this->url . "/course/modules/" . $course_id));
        }

        // Get tests already added to this module
        $data["module_tests"] = $this->db
            ->select("ct.id, ct.course_id, ct.test_id, ct.module_id, 
                     DATE_FORMAT(ct.start_date, '%Y-%m-%d %H:%i:%s') as start_date, 
                     DATE_FORMAT(ct.end_date, '%Y-%m-%d %H:%i:%s') as end_date, 
                     ct.is_active,ct.level,
                     t.title, t.duration, t.instructions as description,t.pass_percentage,
                     (SELECT COUNT(DISTINCT sts.student_id) 
                      FROM student_test_submission sts 
                      WHERE sts.test_id = ct.test_id 
                      AND sts.course_id = ct.course_id 
                      AND sts.module_id = ct.module_id) as submitted_students")
            ->from("course_tests ct")
            ->join("tests t", "t.id = ct.test_id")
            ->where("ct.course_id", $course_id)
            ->where("ct.module_id", $module_id)
            ->where("ct.is_active", 1)
            ->get()
            ->result_array();



      

        // Get total students for the course
        // Get students directly added to the course
        $direct_students = $this->db
            ->select('s.id')
            ->from("course_students cs")
            ->join("students s", "s.id = cs.student_id")
            ->where("cs.course_id", $course_id)
            ->where("cs.is_active", 1)
            ->where("s.is_active", 1)
            ->get()
            ->result_array();

        // Get students via groups
        $group_students = $this->db
            ->select('s.id')
            ->from("course_groups cg")
            ->join("groups g", "g.id = cg.group_id")
            ->join("group_members gm", "gm.group_id = g.id")
            ->join("students s", "s.id = gm.student_id")
            ->where("cg.course_id", $course_id)
            ->where("cg.is_active", 1)
            ->where("s.is_active", 1)
            ->get()
            ->result_array();

        // Get students via departments
        $dept_students = $this->db
            ->select('s.id')
            ->from("course_departments cd")
            ->join("departments d", "d.id = cd.department_id")
            ->join("students s", "s.department = d.id")
            ->where("cd.course_id", $course_id)
            ->where("s.is_active", 1)
            ->get()
            ->result_array();

        // Combine all students and remove duplicates
        $all_students = array_merge(
            $direct_students,
            $group_students,
            $dept_students
        );

        // Remove duplicates based on student ID
        $unique_students = [];
        $student_ids = [];

        foreach ($all_students as $student) {
            if (!in_array($student["id"], $student_ids)) {
                $student_ids[] = $student["id"];
                $unique_students[] = $student;
            }
        }

        $total_students = count($unique_students);

        // Add total_students to each test
        foreach ($data["module_tests"] as &$test) {
            $test['total_students'] = $total_students;
            unset($test);
        }

    


        // Get available tests (tests not already added to this module)
        $test_conditions = [
            "is_active" => 1,
            "college_id" => $this->college["id"],
        ];

        if ($this->permissions["read"] !== "all") {
            $test_conditions["created_by"] = $this->permissions["read"];
        }

        $data["available_tests"] = $this->db_model->get_all("tests", $test_conditions);

        // Get IDs of tests already added to any module in this course
        $course_test_ids = $this->db
            ->select("test_id")
            ->from("course_tests")
            ->where("course_id", $course_id)
            ->where("is_active", 1)
            ->get()
            ->result_array();
        
        $course_test_ids = array_column($course_test_ids, "test_id");
        
        // Filter out tests already added to any module in the course
        foreach ($data["available_tests"] as $key => $test) {
            if (in_array($test["id"], $course_test_ids)) {
                unset($data["available_tests"][$key]);
            }
        }
        $data["available_tests"] = array_values($data["available_tests"]);

        $this->load->view("faculty/faculty/sidebar", $class);
        $this->load->view("course/module_tests", $data);
        $this->load->view("faculty/faculty/footer");
    }

    // all_course_module_tests


    public function all_course_module_tests($course_id = null, $module_id = null)
    {
        if (!$course_id || !$module_id) {
            redirect(base_url($this->url . "/course"));
        }

        $data["url"] = $this->url;
        $class["classname"] = "allcourses";
        $class["url"] = $this->url;
        $map = [
            DESIGNATION_STAFF => "staff",
            DESIGNATION_PRINCIPAL => "principal",
        ];
        $path = $map[$this->session_data["designation"]] ?? "hod";
        $class["sidebar_href"] = base_url($this->url . "/" . $path);
        $data["college_id"] = $this->college["id"];

        // Get course details
        // $data["course"] = $this->db_model->get_row(TABLE_COURCES, [
        //     "id" => $course_id,
        //     "is_active" => 1,
        //     "college_id" => $this->college["id"],
        // ]);
    
        $data["course"] = $this->common->get_course_details($course_id, $this->college['id']);;


        if (!$data["course"]) {
            $this->session->set_flashdata("message", [
                "danger",
                "Course not found.",
            ]);
            redirect(base_url($this->url . "/course"));
        }

        // Get module details
        $data["module"] = $this->db_model->get_row("course_modules", [
            "id" => $module_id,
            "course_id" => $course_id,
            "is_active" => 1,
        ]);

        if (!$data["module"]) {
            $this->session->set_flashdata("message", [
                "danger",
                "Module not found.",
            ]);
            redirect(base_url($this->url . "/course/modules/" . $course_id));
        }

        // Get tests already added to this module
        $data["module_tests"] = $this->db
            ->select("ct.id, ct.course_id, ct.test_id, ct.module_id, 
                     DATE_FORMAT(ct.start_date, '%Y-%m-%d %H:%i:%s') as start_date, 
                     DATE_FORMAT(ct.end_date, '%Y-%m-%d %H:%i:%s') as end_date, 
                     ct.is_active,ct.level,
                     t.title, t.duration, t.instructions as description,t.pass_percentage,
                     (SELECT COUNT(DISTINCT sts.student_id) 
                      FROM student_test_submission sts 
                      WHERE sts.test_id = ct.test_id 
                      AND sts.course_id = ct.course_id 
                      AND sts.module_id = ct.module_id) as submitted_students")
            ->from("course_tests ct")
            ->join("tests t", "t.id = ct.test_id")
            ->where("ct.course_id", $course_id)
            ->where("ct.module_id", $module_id)
            ->where("ct.is_active", 1)
            ->get()
            ->result_array();



      

        // Get total students for the course
        // Get students directly added to the course
        $direct_students = $this->db
            ->select('s.id')
            ->from("course_students cs")
            ->join("students s", "s.id = cs.student_id")
            ->where("cs.course_id", $course_id)
            ->where("cs.is_active", 1)
            ->where("s.is_active", 1)
            ->get()
            ->result_array();

        // Get students via groups
        $group_students = $this->db
            ->select('s.id')
            ->from("course_groups cg")
            ->join("groups g", "g.id = cg.group_id")
            ->join("group_members gm", "gm.group_id = g.id")
            ->join("students s", "s.id = gm.student_id")
            ->where("cg.course_id", $course_id)
            ->where("cg.is_active", 1)
            ->where("s.is_active", 1)
            ->get()
            ->result_array();

        // Get students via departments
        $dept_students = $this->db
            ->select('s.id')
            ->from("course_departments cd")
            ->join("departments d", "d.id = cd.department_id")
            ->join("students s", "s.department = d.id")
            ->where("cd.course_id", $course_id)
            ->where("s.is_active", 1)
            ->get()
            ->result_array();

        // Combine all students and remove duplicates
        $all_students = array_merge(
            $direct_students,
            $group_students,
            $dept_students
        );

        // Remove duplicates based on student ID
        $unique_students = [];
        $student_ids = [];

        foreach ($all_students as $student) {
            if (!in_array($student["id"], $student_ids)) {
                $student_ids[] = $student["id"];
                $unique_students[] = $student;
            }
        }

        $total_students = count($unique_students);

        // Add total_students to each test
        foreach ($data["module_tests"] as &$test) {
            $test['total_students'] = $total_students;
            unset($test);
        }

    


        // Get available tests (tests not already added to this module)
        $test_conditions = [
            "is_active" => 1,
            "college_id" => $this->college["id"],
        ];

        if ($this->permissions["read"] !== "all") {
            $test_conditions["created_by"] = $this->permissions["read"];
        }

        $data["available_tests"] = $this->db_model->get_all("tests", $test_conditions);

        // Get IDs of tests already added to any module in this course
        $course_test_ids = $this->db
            ->select("test_id")
            ->from("course_tests")
            ->where("course_id", $course_id)
            ->where("is_active", 1)
            ->get()
            ->result_array();
        
        $course_test_ids = array_column($course_test_ids, "test_id");
        
        // Filter out tests already added to any module in the course
        foreach ($data["available_tests"] as $key => $test) {
            if (in_array($test["id"], $course_test_ids)) {
                unset($data["available_tests"][$key]);
            }
        }
        $data["available_tests"] = array_values($data["available_tests"]);

        $this->load->view("faculty/faculty/sidebar", $class);
        $this->load->view("course/all_course_module_tests", $data);
        $this->load->view("faculty/faculty/footer");
    }

    public function add_tests_to_module()
    {
        $post = $this->input->post();
        if ($post) {
            $course_id = $this->input->post("course_id");
            $module_id = $this->input->post("module_id");
            $test_ids = $this->input->post("test_ids");
            $start_date = $this->input->post("start_date");
            $end_date = $this->input->post("end_date");
            $explanation_start_date = $this->input->post("explanation_start_date");
            $explanation_end_date = $this->input->post("explanation_end_date");
            $show_explanation = $this->input->post("show_explanation") == "on" ? 1 : 0;
            $level = $this->input->post('level');
            // $pass_score = $this->input->post('pass_score');

            // var_dump($post);die;

            if(!$this->check_permisson($course_id)){

                redirect( base_url( $this->url . '/course' ) );
    
            }
    

            if (empty($test_ids)) {
                $this->session->set_flashdata("message", [
                    "danger",
                    "Please select at least one test to add.",
                ]);
                redirect(base_url($this->url . "/course/module_tests/" . $course_id . "/" . $module_id));
            }
        //Fetch course_mode from DB
        $course = $this->db_model->get_row(TABLE_COURCES, [
            'id' => $course_id,
            'is_active' => 1,
            "college_id" => $this->college["id"],
        ]);
        
            $course_mode = $course['course_mode'] ?? 1;
            $success = true;
            foreach ($test_ids as $test_id) {
                // Check if already exists
                $exists = $this->db_model->get_row("course_tests", [
                    "course_id" => $course_id,
                    "module_id" => $module_id,
                    "test_id" => $test_id,
                    "is_active" => 1,
                ]);

                if (!$exists) {
                    $data = [
                        "course_id" => $course_id,
                        "module_id" => $module_id,
                        "test_id" => $test_id,
                        "is_active" => 1,
                        "created_at" => date('Y-m-d H:i:s'),
                        "show_explanation" =>$show_explanation
                      
                    ];

                if ($course_mode == 2) {  
                    // Check for duplicate level
                    $levelExists = $this->db_model->get_row("course_tests", [
                        "course_id" => $course_id,
                        "module_id" => $module_id,
                        "level" => $level,
                        "is_active" => 1
                    ]);

                    if ($levelExists) {
                        $this->session->set_flashdata("message", [
                            "danger",
                            "Level " . $level . " already exists for this module. Please choose a different level.",
                        ]);
                        redirect(base_url($this->url . "/course/module_tests/" . $course_id . "/" . $module_id));
                    }

                    $data['level'] = $level;
                    // $data['pass_score'] = $pass_score;

                } else {
                    $data['start_date'] = !empty($start_date) ? $start_date : NULL;
                    $data['end_date'] = !empty($end_date) ? $end_date : NULL;
                    
                    // Only set explanation dates if explanation is enabled
                    if ($show_explanation == 1) {
                        // Validate that explanation start date is after test end date
                        if (!empty($end_date) && !empty($explanation_start_date)) {
                            if (strtotime($explanation_start_date) <= strtotime($end_date)) {
                                $this->session->set_flashdata("message", [
                                    "danger",
                                    "Result visibility start date must be after the test end date.",
                                ]);
                                redirect(base_url($this->url . "/course/module_tests/" . $course_id . "/" . $module_id));
                            }
                        }
                        
                        $data['explanation_start_date'] = !empty($explanation_start_date) ? $explanation_start_date : NULL;
                        $data['explanation_end_date'] = !empty($explanation_end_date) ? $explanation_end_date : NULL;
                    } else {
                        // Clear explanation dates when disabled
                        $data['explanation_start_date'] = NULL;
                        $data['explanation_end_date'] = NULL;
                    }
                }

                    if (!$this->db_model->insert("course_tests", $data)) {
                        $success = false;
                    }
                }
            }

            if ($success) {
                $this->session->set_flashdata("message", [
                    "success",
                    "Tests added to module successfully!",
                ]);
            } else {
                $this->session->set_flashdata("message", [
                    "danger",
                    "Failed to add some or all tests to the module.",
                ]);
            }

            redirect(base_url($this->url . "/course/module_tests/" . $course_id . "/" . $module_id));
        } else {
            redirect(base_url($this->url . "/course"));
        }
    }

    public function edit_module_test($course_id = null, $module_id = null, $course_test_id = null)
    {
        if (!$course_id || !$module_id || !$course_test_id) {
            redirect(base_url($this->url . "/course"));
        }

        $post = $this->input->post();
        if ($post) {
            $start_date = $this->input->post("start_date");
            $end_date = $this->input->post("end_date");
            $level = $this->input->post("level");
            // $pass_score = $this->input->post("pass_score");

            if(!$this->check_permisson($course_id)){

                redirect( base_url( $this->url . '/course' ) );
    
            }
    
        // Get course mode
        $course = $this->db_model->get_row(TABLE_COURCES, [
            "id" => $course_id,
            "is_active" => 1,
            "college_id" => $this->college["id"],
        ]);
        $course_mode = $course["course_mode"] ?? 1;

        $data = [];

            // Update course test
        if ($course_mode == 2) {
            $levelExists = $this->db_model->get_row("course_tests", [
                "course_id" => $course_id,
                "module_id" => $module_id,
                "level" => $level,
                "is_active" => 1,
                "id !=" => $course_test_id
            ]);

            if ($levelExists) {
                $this->session->set_flashdata("message", [
                    "danger",
                    "Level " . $level . " already exists for this module. Please choose a different level.",
                ]);
                redirect(base_url($this->url . "/course/edit_module_test/" . $course_id . "/" . $module_id . "/" . $course_test_id));
            }
           
            $data = [
                "level" => $level,
                // "pass_score" => $pass_score,
            ];
        } else {
            $show_explanation = $this->input->post("show_explanation") == "on" ? 1 : 0;
            
            $data = [
                "start_date" => !empty($start_date) ? $start_date : NULL,
                "end_date" => !empty($end_date) ? $end_date : NULL,
                "show_explanation" => $show_explanation,
            ];
            
            // Only set explanation dates if explanation is enabled
            if ($show_explanation == 1) {
                $explanation_start_date = $this->input->post("explanation_start_date");
                $explanation_end_date = $this->input->post("explanation_end_date");
                
                // Validate that explanation start date is after test end date
                if (!empty($end_date) && !empty($explanation_start_date)) {
                    if (strtotime($explanation_start_date) <= strtotime($end_date)) {
                        $this->session->set_flashdata("message", [
                            "danger",
                            "Result visibility start date must be after the test end date.",
                        ]);
                        redirect(base_url($this->url . "/course/edit_module_test/" . $course_id . "/" . $module_id . "/" . $course_test_id));
                    }
                }
                
                $data["explanation_start_date"] = !empty($explanation_start_date) ? $explanation_start_date : NULL;
                $data["explanation_end_date"] = !empty($explanation_end_date) ? $explanation_end_date : NULL;
            } else {
                // Clear explanation dates when disabled
                $data["explanation_start_date"] = NULL;
                $data["explanation_end_date"] = NULL;
            }
        }

            if ($this->db_model->update("course_tests", $data, [
                "id" => $course_test_id,
                "course_id" => $course_id,
                "module_id" => $module_id,
                "is_active" => 1,
            ])) {
                $this->session->set_flashdata("message", [
                    "success",
                    "Test schedule updated successfully!",
                ]);
                redirect(base_url($this->url . "/course/module_tests/" . $course_id . "/" . $module_id));
            } else {
                $this->session->set_flashdata("message", [
                    "danger",
                    "Failed to update test schedule.",
                ]);
                redirect(base_url($this->url . "/course/edit_module_test/" . $course_id . "/" . $module_id . "/" . $course_test_id));
            }
        } else {
            $data["url"] = $this->url;
            $class["classname"] = "courses";
            $class["url"] = $this->url;
            $map = [
                DESIGNATION_STAFF => "staff",
                DESIGNATION_PRINCIPAL => "principal",
            ];
            $path = $map[$this->session_data["designation"]] ?? "hod";
            $class["sidebar_href"] = base_url($this->url . "/" . $path);
            $data["college_id"] = $this->college["id"];

            // Get course details
            $data["course"] = $this->db_model->get_row(TABLE_COURCES, [
                "id" => $course_id,
                "is_active" => 1,
                "college_id" => $this->college["id"],
            ]);

            if (!$data["course"]) {
                $this->session->set_flashdata("message", [
                    "danger",
                    "Course not found.",
                ]);
                redirect(base_url($this->url . "/course"));
            }

            // Get module details
            $data["module"] = $this->db_model->get_row("course_modules", [
                "id" => $module_id,
                "course_id" => $course_id,
                "is_active" => 1,
            ]);

            if (!$data["module"]) {
                $this->session->set_flashdata("message", [
                    "danger",
                    "Module not found.",
                ]);
                redirect(base_url($this->url . "/course/modules/" . $course_id));
            }

            // Get course test details
            $data["course_test"] = $this->db
                ->select("ct.*, t.title as test_name, t.instructions as description")
                ->from("course_tests ct")
                ->join("tests t", "t.id = ct.test_id")
                ->where("ct.id", $course_test_id)
                ->where("ct.course_id", $course_id)
                ->where("ct.module_id", $module_id)
                ->where("ct.is_active", 1)
                ->get()
                ->row_array();

            if (!$data["course_test"]) {
                $this->session->set_flashdata("message", [
                    "danger",
                    "Test not found in this module.",
                ]);
                redirect(base_url($this->url . "/course/module_tests/" . $course_id . "/" . $module_id));
            }

            $this->load->view("faculty/faculty/sidebar", $class);
            $this->load->view("course/edit_module_test", $data);
            $this->load->view("faculty/faculty/footer");
        }
    }

    public function remove_test_from_module($course_id = null, $module_id = null, $course_test_id = null)
    {
        if (!$course_id || !$module_id || !$course_test_id) {
            redirect(base_url($this->url . "/course"));
        }

        if(!$this->check_permisson($course_id)){

            redirect( base_url( $this->url . '/course' ) );

        }


        // Deactivate test in module
        if ($this->db_model->update("course_tests", ["is_active" => 0], [
            "id" => $course_test_id,
            "course_id" => $course_id,
            "module_id" => $module_id,
        ])) {
            $this->session->set_flashdata("message", [
                "success",
                "Test removed from module successfully!",
            ]);
        } else {
            $this->session->set_flashdata("message", [
                "danger",
                "Failed to remove test from module.",
            ]);
        }

        redirect(base_url($this->url . "/course/module_tests/" . $course_id . "/" . $module_id));
    }

    public function test_questions($test_id = null) {
        if (!$test_id) {
            $this->session->set_flashdata("message", [
                "danger",
                "Invalid test ID.",
            ]);
            redirect(base_url($this->url . "/course"));
        }

        // Get course test details
        $course_test = $this->db_model->get_row("course_tests", [
            "id" => $test_id,
            "is_active" => 1
        ]);

        if (!$course_test) {
            $this->session->set_flashdata("message", [
                "danger",
                "Test not found.",
            ]);
            redirect(base_url($this->url . "/course"));
        }

        // Set up view data
        $data["url"] = $this->url;
        $data["route"] = "course";
        $class["classname"] = "courses";
        $class["url"] = $this->url;
        $map = [
            DESIGNATION_STAFF => "staff",
            DESIGNATION_PRINCIPAL => "principal",
        ];
        $path = $map[$this->session_data["designation"]] ?? "hod";
        $class["sidebar_href"] = base_url($this->url . "/" . $path);
        $data["college_id"] = $this->college["id"];

        // Get test details
        $data["test"] = $this->db_model->get_row("tests", [
            "id" => $course_test["test_id"],
            "is_active" => 1,
            "college_id" => $this->college["id"],
        ]);

        if (!$data["test"]) {
            $this->session->set_flashdata("message", [
                "danger",
                "Test not found.",
            ]);
            redirect(base_url($this->url . "/course"));
        }

        // Test-level flag from UI settings
        $sections_enabled = $this->common->is_sections_enabled_for_test($course_test["test_id"]);

        // Get questions with all related data
        $this->db->select([
            'tq.*',
            'q.*',
            'dl.level as difficulty_level',
            'qt.type as question_type',
            'qst.sub_type as question_sub_type',
            'ts.section_name'
        ]);
        $this->db->from('test_questions tq');
        $this->db->join('question_bank q', 'q.id = tq.question_id');
        $this->db->join('question_difficulty_level dl', 'dl.id = q.difficulty_level', 'left');
        $this->db->join('question_types qt', 'qt.id = q.type', 'left');
        $this->db->join('question_sub_types qst', 'qst.id = q.sub_type', 'left');
        $this->db->join('test_sections ts', 'ts.id = tq.section_id', 'left');
        $this->db->where('tq.test_id', $course_test["test_id"]);
        $this->db->where('tq.is_active', 1);
        $this->db->where('q.is_active', 1);
        if ($sections_enabled && defined('DEFAULT_SECTION_ID')) {
            $this->db->where('tq.section_id !=', DEFAULT_SECTION_ID);
        }
        $this->db->order_by('tq.question_order', 'asc');

        $questions = $this->db->get()->result_array();

        if ($questions === FALSE) {
            $data["questions"] = [];
            $this->session->set_flashdata("message", [
                "danger",
                "Error fetching questions: " . $this->db->error()['message']
            ]);
        } else {
            // Process each question to get additional data
            foreach ($questions as &$question) {
                // Get options for MCQ questions
                if ($question['type'] == 1) {
                    $question['options'] = $this->db_model->get_all(
                        'answer_options',
                        [
                            'question_id' => $question['id'],
                            'is_active' => 1
                        ]
                    );
                }

                // Get test cases for CODE questions
                if ($question['type'] == 2) {
                    $question['test_cases'] = $this->db_model->get_all(
                        'question_test_cases',
                        ['question_id' => $question['id']]
                    );
                }

                // Format tags as array
                $question['tags_array'] = !empty($question['tags']) ?
                    array_map('trim', explode(',', $question['tags'])) : [];

                // Handle section assignment directly
                if (!empty($question['section_id'])) {
                    $section_id = (int)$question['section_id'];
                    if (defined('DEFAULT_SECTION_ID') && $section_id === (int)DEFAULT_SECTION_ID) {
                        $question['section_id'] = 'unassigned';
                        $question['section_name'] = 'Unassigned';
                    } elseif (!empty($question['section_name'])) {
                        $question['section_id'] = $section_id;
                        $question['section_name'] = $question['section_name'];
                    } else {
                        $question['section_id'] = $section_id;
                        $question['section_name'] = 'Unknown Section';
                    }
                } else {
                    // For normal tests, section_id is NULL, don't set section_name
                    $question['section_id'] = null;
                    $question['section_name'] = null;
                }
            }

            $data["questions"] = $questions;
            $data ["course_test"] =  $course_test;
            $data["back_url"] = base_url($this->url . "/course/module_tests/" . $course_test["course_id"] . "/" . $course_test["module_id"]);

            unset($question);

            // Build section context using UI flag (avoid re-detecting via loops)
            $section_context = $this->common->build_section_context($data["questions"], $sections_enabled);
            $data['sections_enabled'] = $sections_enabled && $section_context['enabled'];
            $data['section_map'] = $section_context['map'];
            $data['questions_by_section'] = $section_context['grouped'];
            $data['section_order'] = $section_context['order'];
        }

        // Load views
        $this->load->view("faculty/faculty/sidebar", $class);
        $this->load->view("course/test_questions", $data);
        $this->load->view("faculty/faculty/footer");
    }

    public function all_course_test_questions($test_id = null) {
        if (!$test_id) {
            $this->session->set_flashdata("message", [
                "danger",
                "Invalid test ID.",
            ]);
            redirect(base_url($this->url . "/course"));
        }

        // Get course test details
        $course_test = $this->db_model->get_row("course_tests", [
            "id" => $test_id,
            "is_active" => 1
        ]);

        if (!$course_test) {
            $this->session->set_flashdata("message", [
                "danger",
                "Test not found.",
            ]);
            redirect(base_url($this->url . "/course"));
        }

        // Set up view data
        $data["url"] = $this->url;
        $data["route"] = "allcourses";
        $class["classname"] = "allcourses";
        $class["url"] = $this->url;
        $map = [
            DESIGNATION_STAFF => "staff",
            DESIGNATION_PRINCIPAL => "principal",
        ];
        $path = $map[$this->session_data["designation"]] ?? "hod";
        $class["sidebar_href"] = base_url($this->url . "/" . $path);
        $data["college_id"] = $this->college["id"];

        // Get test details
        // $data["test"] = $this->db_model->get_row("tests", [
        //     "id" => $course_test["test_id"],
        //     "is_active" => 1,
        //     "college_id" => $this->college["id"],
        // ]);

        $data["test"] = $this->common->get_test_details($course_test["test_id"],$this->college['id']);

        if (!$data["test"]) {
            $this->session->set_flashdata("message", [
                "danger",
                "Test not found.",
            ]);
            redirect(base_url($this->url . "/course"));
        }

        // Test-level flag from UI settings (use common helper)
        $sections_enabled = $this->common->is_sections_enabled_for_test($course_test["test_id"]);

        // Get questions with all related data
        $this->db->select([
            'tq.*',
            'q.*',
            'dl.level as difficulty_level',
            'qt.type as question_type',
            'qst.sub_type as question_sub_type',
            'ts.section_name'
        ]);
        $this->db->from('test_questions tq');
        $this->db->join('question_bank q', 'q.id = tq.question_id');
        $this->db->join('question_difficulty_level dl', 'dl.id = q.difficulty_level', 'left');
        $this->db->join('question_types qt', 'qt.id = q.type', 'left');
        $this->db->join('question_sub_types qst', 'qst.id = q.sub_type', 'left');
        $this->db->join('test_sections ts', 'ts.id = tq.section_id', 'left');
        $this->db->where('tq.test_id', $course_test["test_id"]);
        $this->db->where('tq.is_active', 1);
        $this->db->where('q.is_active', 1);
        if ($sections_enabled && defined('DEFAULT_SECTION_ID')) {
            $this->db->where('tq.section_id !=', DEFAULT_SECTION_ID);
        }
        $this->db->order_by('tq.question_order', 'asc');

        $questions = $this->db->get()->result_array();

        if ($questions === FALSE) {
            $data["questions"] = [];
            $this->session->set_flashdata("message", [
                "danger",
                "Error fetching questions: " . $this->db->error()['message']
            ]);
        } else {
            // Process each question to get additional data
            foreach ($questions as &$question) {
                // Get options for MCQ questions
                if ($question['type'] == 1) {
                    $question['options'] = $this->db_model->get_all(
                        'answer_options',
                        [
                            'question_id' => $question['id'],
                            'is_active' => 1
                        ]
                    );
                }

                // Get test cases for CODE questions
                if ($question['type'] == 2) {
                    $question['test_cases'] = $this->db_model->get_all(
                        'question_test_cases',
                        ['question_id' => $question['id']]
                    );
                }

                // Format tags as array
                $question['tags_array'] = !empty($question['tags']) ?
                    array_map('trim', explode(',', $question['tags'])) : [];

                // Handle section assignment directly
                if (!empty($question['section_id'])) {
                    $section_id = (int)$question['section_id'];
                    if (defined('DEFAULT_SECTION_ID') && $section_id === (int)DEFAULT_SECTION_ID) {
                        $question['section_id'] = 'unassigned';
                        $question['section_name'] = 'Unassigned';
                    } elseif (!empty($question['section_name'])) {
                        $question['section_id'] = $section_id;
                        $question['section_name'] = $question['section_name'];
                    } else {
                        $question['section_id'] = $section_id;
                        $question['section_name'] = 'Unknown Section';
                    }
                } else {
                    // For normal tests, section_id is NULL, don't set section_name
                    $question['section_id'] = null;
                    $question['section_name'] = null;
                }
            }

            $data["questions"] = $questions;
            $data ["course_test"] =  $course_test;
            $data["back_url"] = base_url($this->url . "/allcourses/module_tests/" . $course_test["course_id"] . "/" . $course_test["module_id"]);

            unset($question);

            $section_context = $this->common->build_section_context($data["questions"], $sections_enabled);
            $data['sections_enabled'] = $sections_enabled && $section_context['enabled'];
            $data['section_map'] = $section_context['map'];
            $data['questions_by_section'] = $section_context['grouped'];
            $data['section_order'] = $section_context['order'];
        }

        // Load views
        $this->load->view("faculty/faculty/sidebar", $class);
        $this->load->view("course/test_questions", $data);
        $this->load->view("faculty/faculty/footer");
    }

    // Helper function to format time spent
    private function format_time_spent($seconds) {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        
        $time_display = '';
        if ($hours > 0) {
            $time_display .= $hours . 'h ';
        }
        if ($minutes > 0 || $hours > 0) {
            $time_display .= $minutes . 'm ';
        }
        $time_display .= $seconds . 's';
        
        return $time_display;
    }

    public function test_results($course_id = null, $module_id = null, $test_id = null) {
        if (!$course_id || !$module_id || !$test_id) {
            redirect(base_url($this->url . "/course"));
        }


        if(!$this->check_permisson($course_id)){

            redirect( base_url( $this->url . '/course' ) );

        }


        $data["url"] = $this->url;
        $class["classname"] = "courses";
        $data['route'] = 'course';
        $class["url"] = $this->url;
        $map = [
            DESIGNATION_STAFF => "staff",
            DESIGNATION_PRINCIPAL => "principal",
        ];
        $path = $map[$this->session_data["designation"]] ?? "hod";
        $class["sidebar_href"] = base_url($this->url . "/" . $path);
        $data["college_id"] = $this->college["id"];

        // Get course details
        $data["course"] = $this->db_model->get_row(TABLE_COURCES, [
            "id" => $course_id,
            "is_active" => 1,
            "college_id" => $this->college["id"],
        ]);

        if (!$data["course"]) {
            $this->session->set_flashdata("message", ["danger", "Course not found."]);
            redirect(base_url($this->url . "/course"));
        }

        // Get module details
        $data["module"] = $this->db_model->get_row("course_modules", [
            "id" => $module_id,
            "course_id" => $course_id,
            "is_active" => 1,
        ]);

        if (!$data["module"]) {
            $this->session->set_flashdata("message", ["danger", "Module not found."]);
            redirect(base_url($this->url . "/course/modules/" . $course_id));
        }

        // Get test details with course test information
        $data["test"] = $this->db
            ->select("t.*, ct.start_date as course_start_date, ct.end_date as course_end_date, ct.result_cache")
            ->from("tests t")
            ->join("course_tests ct", "ct.test_id = t.id")
            ->where("t.id", $test_id)
            ->where("ct.course_id", $course_id)
            ->where("ct.module_id", $module_id)
            ->where("ct.is_active", 1)
            ->get()
            ->row_array();

        if (!$data["test"]) {
            $this->session->set_flashdata("message", ["danger", "Test not found."]);
            redirect(base_url($this->url . "/course/module_tests/" . $course_id . "/" . $module_id));
        }

        // Get total students enrolled in the course
        // Get students directly added to the course
        $direct_students = $this->db
            ->select('s.id')
            ->from("course_students cs")
            ->join("students s", "s.id = cs.student_id")
            ->where("cs.course_id", $course_id)
            ->where("cs.is_active", 1)
            ->where("s.is_active", 1)
            ->get()
            ->result_array();

        // Get students via groups
        $group_students = $this->db
            ->select('s.id')
            ->from("course_groups cg")
            ->join("groups g", "g.id = cg.group_id")
            ->join("group_members gm", "gm.group_id = g.id")
            ->join("students s", "s.id = gm.student_id")
            ->where("cg.course_id", $course_id)
            ->where("cg.is_active", 1)
            ->where("s.is_active", 1)
            ->get()
            ->result_array();

        // Get students via departments
        $dept_students = $this->db
            ->select('s.id')
            ->from("course_departments cd")
            ->join("departments d", "d.id = cd.department_id")
            ->join("students s", "s.department = d.id")
            ->where("cd.course_id", $course_id)
            ->where("s.is_active", 1)
            ->get()
            ->result_array();

        // Combine all students and remove duplicates
        $all_students = array_merge(
            $direct_students,
            $group_students,
            $dept_students
        );

        // Remove duplicates based on student ID
        $unique_students = [];
        $student_ids = [];

        foreach ($all_students as $student) {
            if (!in_array($student["id"], $student_ids)) {
                $student_ids[] = $student["id"];
                $unique_students[] = $student;
            }
        }

        $total_students = count($unique_students);
       
        if (!empty($student_ids)) {
        // Get student submissions
        $raw_submissions = $this->db
            ->select("sts.*, s.name as student_name, s.email as student_email, d.name as department_name, s.registration_number, s.external_id")
            ->from("student_test_submission sts")
            ->join("students s", "s.id = sts.student_id", "left")
            ->join("departments d", "d.id = s.department", "left")
            ->where("sts.course_id", $course_id)
            ->where("sts.module_id", $module_id)
            ->where("sts.test_id", $test_id)
            ->where_in("sts.student_id", array_column($unique_students, 'id'))
            ->get()
            ->result_array();


        $data["submissions"] = $raw_submissions;
        // First sync : check sync_count values
        $is_first_sync = true;

        foreach ($raw_submissions as $submission) {
            if ((int)$submission['sync_count'] > 0) {
                 // If ANY student has a sync_count > 0, it's NOT the first sync
                $is_first_sync = false;
                break;
            } 
        }
        $data['first_time_sync'] = $is_first_sync;

        } else {
            $data["submissions"] = [];
        }
        
        // print_r($data);
        // exit;
        // Initialize status counts
        $in_progress_count = 0;
        $completed_count = 0;
        $not_attempted_count = 0;

        // Get test details for OneCompiler API
        $test_details = $this->db_model->get_row('tests', ['id' => $test_id], 'challenge_id');

        // Create a map of submitted student IDs
        $submitted_student_ids = array_column($data["submissions"], 'student_id');

        // Get all students with their details
        if (!empty($student_ids)) {
        $all_students = $this->db
            ->select("s.id as student_id, s.name as student_name, s.email as student_email, d.name as department_name, s.registration_number, s.external_id")
            ->from("students s")
            ->join("departments d", "d.id = s.department", "left")
            ->where_in("s.id", array_column($unique_students, 'id'))
            ->get()
            ->result_array();
        } else {
            $all_students = [];
        }
        // Create a new array for all submissions
        $all_submissions = [];

        // Check if we should use cached results or call API FIRST (moved up)
        $current_time = time();
        $end_time = strtotime($data["test"]["course_end_date"]);
        $api_data = [];

        // If test has ended and we have cached results, use them
        if ($current_time > $end_time && !empty($data["test"]["result_cache"])) {
            $api_data = json_decode($data["test"]["result_cache"], true);
        } else {
            // Make API call to get all student results
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://onecompiler.com/api/v1/challenges/stats?access_token=' . ONE_COMPILER_API_KEY . '&challengeIds=' . $test_details['challenge_id'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));
            
            $api_response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if (!$err) {
                $api_data = json_decode($api_response, true);
                
                // If test has ended, cache the results
                if ($current_time > $end_time) {
                    $this->db_model->update("course_tests", 
                        ["result_cache" => $api_response],
                        [
                            "course_id" => $course_id,
                            "module_id" => $module_id,
                            "test_id" => $test_id
                        ]
                    );
                }
            }
        }

        // First add all submitted students with extra check
        foreach ($data["submissions"] as $submission) {
            // Check if student has DB submission
            if (isset($submission['finished']) && $submission['finished'] == 1) {
                // Finished in DB, mark as completed
                $submission['status'] = 'completed';
                $completed_count++;
            } else {
                // Not finished in DB, check OneCompiler API
                $status = 'in_progress'; // Default
                $found_in_api = false;

                if (isset($api_data['stats'][$test_details['challenge_id']])) {
                    foreach ($api_data['stats'][$test_details['challenge_id']] as $result) {
                        // Match by external_id from student record
                        $student_ext_id = $this->db->select('external_id')
                            ->from('students')
                            ->where('id', $submission['student_id'])
                            ->get()
                            ->row_array();
                        
                        if ($student_ext_id && isset($result['user']['_id']) && $result['user']['_id'] === $student_ext_id['external_id']) {
                            $found_in_api = true;
                            if (isset($result['finished']) && $result['finished'] == true) {
                                $status = 'completed';
                                $completed_count++;
                                // Update DB to mark as finished
                                $this->db->where('id', $submission['id'])
                                    ->update('student_test_submission', ['finished' => 1]);
                            } else {
                                $status = 'in_progress';
                                $in_progress_count++;
                            }
                            break;
                        }
                    }
                }

                if (!$found_in_api) {
                    // Not found in OneCompiler, mark as completed based on DB
                    $status = 'completed';
                    $completed_count++;
                }

                $submission['status'] = $status;
            }

            $all_submissions[$submission['student_id']] = $submission;
        }

        // Process non-submitted students
        foreach ($all_students as $student) {
            if (!isset($all_submissions[$student['student_id']])) {
                $status = 'not_attempted';
                $found = false;

                if (isset($api_data['stats'][$test_details['challenge_id']])) {
                    foreach ($api_data['stats'][$test_details['challenge_id']] as $result) {
                        if ($result['user']['_id'] === $student['external_id']) {
                            $found = true;
                            if (isset($result['finished']) && $result['finished'] == true) {
                                $status = 'completed';
                                $completed_count++;
                            } else {
                                $status = 'in_progress';
                                $in_progress_count++;
                            }
                            break;
                        }
                    }
                }

                if (!$found) {
                    $not_attempted_count++;
                }

                // Add non-submitted student to submissions array
                $all_submissions[$student['student_id']] = array_merge($student, [
                    'status' => $status,
                    'percentage' => 0,
                    'earned_score' => 0,
                    'total_score' => 0,
                    'course_id' => $course_id,
                    'module_id' => $module_id,
                    'test_id' => $test_id,
                    'challenge_id' => $test_details['challenge_id'],
                    'challenge_user_id' => $student['external_id'],
                    'submission_time' => null,
                    'tab_changes' => 0,
                    'finished' => 0,
                    'details' => '[]'
                ]);
            }
        }

        // Convert associative array to indexed array and sort by student name
        $data["submissions"] = array_values($all_submissions);
        usort($data["submissions"], function($a, $b) {
            return strcmp($a['student_name'], $b['student_name']);
        });

        // Calculate statistics
        $pass_count = 0;
        $total_score = 0;

        foreach ($data["submissions"] as $submission) {
            if ($submission["percentage"] >= $data["test"]["pass_percentage"]) {
                $pass_count++;
            }
            $total_score += $submission["percentage"];
        }

        $data["completed_submissions"] = $completed_count;
        $data["in_progress_count"] = $in_progress_count;
        $data["not_attempted_count"] = $not_attempted_count;
        $data["total_students"] = $total_students;
        $data["pass_rate"] = $completed_count > 0 ? ($pass_count / $completed_count) * 100 : 0;
        $data["avg_score"] = $completed_count > 0 ? $total_score / $completed_count : 0;

        // var_dump($data);
        // var_dump("Now: ", time());
        // var_dump("Course End Date: ", strtotime($data['test']['course_end_date']));
        // var_dump("In Progress: ", $in_progress_count);
        // var_dump("Not Attempted: ", $not_attempted_count);
        // var_dump("Is First Sync: ", $is_first_sync);

        // exit;
        $this->load->view("faculty/faculty/sidebar", $class);
        $this->load->view("course/test_results", $data);
        $this->load->view("faculty/faculty/footer");
    }

    // all_course_test_results

    public function all_course_test_results($course_id = null, $module_id = null, $test_id = null) {

        if (!$course_id || !$module_id || !$test_id) {
            redirect(base_url($this->url . "/course"));
        }

        $data["url"] = $this->url;
        $class["classname"] = "allcourses";
        $data['route'] = 'allcourses';
        $class["url"] = $this->url;
        $map = [
            DESIGNATION_STAFF => "staff",
            DESIGNATION_PRINCIPAL => "principal",
        ];
        $path = $map[$this->session_data["designation"]] ?? "hod";
        $class["sidebar_href"] = base_url($this->url . "/" . $path);
        $data["college_id"] = $this->college["id"];


          // Get course details
        // $data["course"] = $this->db_model->get_row(TABLE_COURCES, [
        //     "id" => $course_id,
        //     "is_active" => 1,
        //     "college_id" => $this->college["id"],
        // ]);
         // Modified: Fetch course from either direct or shared (special) mapping
            
            $data["course"] = $this->common->get_course_details($course_id, $this->college['id']);;

        if (!$data["course"]) {
            $this->session->set_flashdata("message", ["danger", "Course not found."]);
            redirect(base_url($this->url . "/course"));
        }

        // Get module details
        $data["module"] = $this->db_model->get_row("course_modules", [
            "id" => $module_id,
            "course_id" => $course_id,
            "is_active" => 1,
        ]);

        if (!$data["module"]) {
            $this->session->set_flashdata("message", ["danger", "Module not found."]);
            redirect(base_url($this->url . "/course/modules/" . $course_id));
        }

        // Get test details with course test information
        $data["test"] = $this->db
            ->select("t.*, ct.start_date as course_start_date, ct.end_date as course_end_date, ct.result_cache")
            ->from("tests t")
            ->join("course_tests ct", "ct.test_id = t.id")
            ->where("t.id", $test_id)
            ->where("ct.course_id", $course_id)
            ->where("ct.module_id", $module_id)
            ->where("ct.is_active", 1)
            ->get()
            ->row_array();

        if (!$data["test"]) {
            $this->session->set_flashdata("message", ["danger", "Test not found."]);
            redirect(base_url($this->url . "/course/module_tests/" . $course_id . "/" . $module_id));
        }

        // Get total students enrolled in the course
        // Get students directly added to the course
        $direct_students = $this->db
            ->select('s.id')
            ->from("course_students cs")
            ->join("students s", "s.id = cs.student_id")
            ->where("cs.course_id", $course_id)
            ->where("cs.is_active", 1)
            ->where("s.is_active", 1)
            ->where("s.college_id", $this->college["id"])
            ->get()
            ->result_array();

        // Get students via groups
        $group_students = $this->db
            ->select('s.id')
            ->from("course_groups cg")
            ->join("groups g", "g.id = cg.group_id")
            ->join("group_members gm", "gm.group_id = g.id")
            ->join("students s", "s.id = gm.student_id")
            ->where("cg.course_id", $course_id)
            ->where("cg.is_active", 1)
            ->where("s.is_active", 1)
            ->where("s.college_id", $this->college["id"])
            ->get()
            ->result_array();

        // Get students via departments
        $dept_students = $this->db
            ->select('s.id')
            ->from("course_departments cd")
            ->join("departments d", "d.id = cd.department_id")
            ->join("students s", "s.department = d.id")
            ->where("cd.course_id", $course_id)
            ->where("s.is_active", 1)
            ->where("s.college_id", $this->college["id"])
            ->get()
            ->result_array();

        // Combine all students and remove duplicates
        $all_students = array_merge(
            $direct_students,
            $group_students,
            $dept_students
        );

        // Remove duplicates based on student ID
        $unique_students = [];
        $student_ids = [];

        foreach ($all_students as $student) {
            if (!in_array($student["id"], $student_ids)) {
                $student_ids[] = $student["id"];
                $unique_students[] = $student;
            }
        }

        $total_students = count($unique_students);
        // print_r($student_ids);
        // exit;
        
    if (!empty($student_ids)) {
        // Get student submissions
        $raw_submissions = $this->db
            ->select("sts.*, s.name as student_name, s.email as student_email, d.name as department_name, s.registration_number, s.external_id")
            ->from("student_test_submission sts")
            ->join("students s", "s.id = sts.student_id", "left")
            ->join("departments d", "d.id = s.department", "left")
            ->where("sts.course_id", $course_id)
            ->where("sts.module_id", $module_id)
            ->where("sts.test_id", $test_id)
            ->where_in("sts.student_id", array_column($unique_students, 'id'))
            ->get()
            ->result_array();

        $data["submissions"] = $raw_submissions;
        // First sync : check if sync has been attempted before
        $is_first_sync = true;

        foreach ($raw_submissions as $submission) {
            if ((int)$submission['sync_count'] > 0) {
                 // If ANY student has a sync_count > 0, it's NOT the first sync
                $is_first_sync = false;
                break;
            } 
        }
        $data['first_time_sync'] = $is_first_sync;

    } else {
        $data["submissions"] = [];
    }
        // print_r($data);
        // exit;
        // Initialize status counts
        $in_progress_count = 0;
        $completed_count = 0;
        $not_attempted_count = 0;

        // Get test details for OneCompiler API
        $test_details = $this->db_model->get_row('tests', ['id' => $test_id], 'challenge_id');

        // Create a map of submitted student IDs
        $submitted_student_ids = array_column($data["submissions"], 'student_id');

        // Get all students with their details
        if (!empty($student_ids)) {
        $all_students = $this->db
            ->select("s.id as student_id, s.name as student_name, s.email as student_email, d.name as department_name, s.registration_number, s.external_id")
            ->from("students s")
            ->join("departments d", "d.id = s.department", "left")
            ->where_in("s.id", array_column($unique_students, 'id'))
            ->get()
            ->result_array();
        } else {
        $all_students = [];
        }
        // Create a new array for all submissions
        $all_submissions = [];

        // Check if we should use cached results or call API FIRST (moved up)
        $current_time = time();
        $end_time = strtotime($data["test"]["course_end_date"]);
        $api_data = [];

        // If test has ended and we have cached results, use them
        if ($current_time > $end_time && !empty($data["test"]["result_cache"])) {
            $api_data = json_decode($data["test"]["result_cache"], true);
        } else {
            // Make API call to get all student results
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://onecompiler.com/api/v1/challenges/stats?access_token=' . ONE_COMPILER_API_KEY . '&challengeIds=' . $test_details['challenge_id'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));
            
            $api_response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if (!$err) {
                $api_data = json_decode($api_response, true);
                
                // If test has ended, cache the results
                if ($current_time > $end_time) {
                    $this->db_model->update("course_tests", 
                        ["result_cache" => $api_response],
                        [
                            "course_id" => $course_id,
                            "module_id" => $module_id,
                            "test_id" => $test_id
                        ]
                    );
                }
            }
        }

        // First add all submitted students with extra check
        foreach ($data["submissions"] as $submission) {
            // Check if student has DB submission
            if (isset($submission['finished']) && $submission['finished'] == 1) {
                // Finished in DB, mark as completed
                $submission['status'] = 'completed';
                $completed_count++;
            } else {
                // Not finished in DB, check OneCompiler API
                $status = 'in_progress'; // Default
                $found_in_api = false;

                if (isset($api_data['stats'][$test_details['challenge_id']])) {
                    foreach ($api_data['stats'][$test_details['challenge_id']] as $result) {
                        // Match by external_id from student record
                        $student_ext_id = $this->db->select('external_id')
                            ->from('students')
                            ->where('id', $submission['student_id'])
                            ->get()
                            ->row_array();
                        
                        if ($student_ext_id && isset($result['user']['_id']) && $result['user']['_id'] === $student_ext_id['external_id']) {
                            $found_in_api = true;
                            if (isset($result['finished']) && $result['finished'] == true) {
                                $status = 'completed';
                                $completed_count++;
                                // Update DB to mark as finished
                                $this->db->where('id', $submission['id'])
                                    ->update('student_test_submission', ['finished' => 1]);
                            } else {
                                $status = 'in_progress';
                                $in_progress_count++;
                            }
                            break;
                        }
                    }
                }

                if (!$found_in_api) {
                    // Not found in OneCompiler, mark as completed based on DB
                    $status = 'completed';
                    $completed_count++;
                }

                $submission['status'] = $status;
            }

            $all_submissions[$submission['student_id']] = $submission;
        }

        // Process non-submitted students
        foreach ($all_students as $student) {
            if (!isset($all_submissions[$student['student_id']])) {
                $status = 'not_attempted';
                $found = false;

                if (isset($api_data['stats'][$test_details['challenge_id']])) {
                    foreach ($api_data['stats'][$test_details['challenge_id']] as $result) {
                        if ($result['user']['_id'] === $student['external_id']) {
                            $found = true;
                            if (isset($result['finished']) && $result['finished'] == true) {
                                $status = 'completed';
                                $completed_count++;
                            } else {
                                $status = 'in_progress';
                                $in_progress_count++;
                            }
                            break;
                        }
                    }
                }

                if (!$found) {
                    $not_attempted_count++;
                }

                // Add non-submitted student to submissions array
                $all_submissions[$student['student_id']] = array_merge($student, [
                    'status' => $status,
                    'percentage' => 0,
                    'earned_score' => 0,
                    'total_score' => 0,
                    'course_id' => $course_id,
                    'module_id' => $module_id,
                    'test_id' => $test_id,
                    'challenge_id' => $test_details['challenge_id'],
                    'challenge_user_id' => $student['external_id'],
                    'submission_time' => null,
                    'tab_changes' => 0,
                    'finished' => 0,
                    'details' => '[]'
                ]);
            }
        }

        // Convert associative array to indexed array and sort by student name
        $data["submissions"] = array_values($all_submissions);
        usort($data["submissions"], function($a, $b) {
            return strcmp($a['student_name'], $b['student_name']);
        });

        // Calculate statistics
        $pass_count = 0;
        $total_score = 0;

        foreach ($data["submissions"] as $submission) {
            if ($submission["percentage"] >= $data["test"]["pass_percentage"]) {
                $pass_count++;
            }
            $total_score += $submission["percentage"];
        }

        $data["completed_submissions"] = $completed_count;
        $data["in_progress_count"] = $in_progress_count;
        $data["not_attempted_count"] = $not_attempted_count;
        $data["total_students"] = $total_students;
        $data["pass_rate"] = $completed_count > 0 ? ($pass_count / $completed_count) * 100 : 0;
        $data["avg_score"] = $completed_count > 0 ? $total_score / $completed_count : 0;

   


        // $data["first_time_sync"] = $sync_log ? false : true;
        // var_dump($data);
        // var_dump("Now: ", time());
        // var_dump("Course End Date: ", strtotime($data['test']['course_end_date']));
        // var_dump("In Progress: ", $in_progress_count);
        // var_dump("Not Attempted: ", $not_attempted_count);
        // var_dump("Is First Sync: ", $is_first_sync);

        // exit;
        $this->load->view("faculty/faculty/sidebar", $class);
        $this->load->view("course/test_results", $data);
        $this->load->view("faculty/faculty/footer");
    }


    public function student_test_report($course_id = null, $module_id = null, $test_id = null, $student_id = null) {
        if (!$course_id || !$module_id || !$test_id || !$student_id) {
            redirect(base_url($this->url . "/course"));
        }

        if(!$this->check_permisson($course_id)){

            redirect( base_url( $this->url . '/course' ) );

        }

        

        $data["url"] = $this->url;
        $class["classname"] = "courses";
        $data['route'] = 'course';
        $class["url"] = $this->url;
        $map = [
            DESIGNATION_STAFF => "staff",
            DESIGNATION_PRINCIPAL => "principal",
        ];
        $path = $map[$this->session_data["designation"]] ?? "hod";
        $class["sidebar_href"] = base_url($this->url . "/" . $path);
        $data["college_id"] = $this->college["id"];

        // Get course details
        $data["course"] = $this->db_model->get_row(TABLE_COURCES, [
            "id" => $course_id,
            "is_active" => 1,
            "college_id" => $this->college["id"],
        ]);

        if (!$data["course"]) {
            $this->session->set_flashdata("message", ["danger", "Course not found."]);
            redirect(base_url($this->url . "/course"));
        }

        // Get module details
        $data["module"] = $this->db_model->get_row("course_modules", [
            "id" => $module_id,
            "course_id" => $course_id,
            "is_active" => 1,
        ]);

        if (!$data["module"]) {
            $this->session->set_flashdata("message", ["danger", "Module not found."]);
            redirect(base_url($this->url . "/course/modules/" . $course_id));
        }

        // Get test details
        $data["test"] = $this->db
            ->select("t.*, ct.start_date, ct.end_date")
            ->from("tests t")
            ->join("course_tests ct", "ct.test_id = t.id")
            ->where("t.id", $test_id)
            ->where("ct.course_id", $course_id)
            ->where("ct.module_id", $module_id)
            ->where("ct.is_active", 1)
            ->get()
            ->row_array();

        if (!$data["test"]) {
            $this->session->set_flashdata("message", ["danger", "Test not found."]);
            redirect(base_url($this->url . "/course/module_tests/" . $course_id . "/" . $module_id));
        }

        // Get student details
        $data["student"] = $this->db
            ->select("s.*, d.name as department_name")
            ->from("students s")
            ->join("departments d", "d.id = s.department")
            ->where("s.id", $student_id)
            ->where("s.is_active", 1)
            ->get()
            ->row_array();

        if (!$data["student"]) {
            $this->session->set_flashdata("message", ["danger", "Student not found."]);
            redirect(base_url($this->url . "/course/test_results/" . $course_id . "/" . $module_id . "/" . $test_id));
        }

        // Get student's submission
        $data["submission"] = $this->db_model->get_row("student_test_submission", [
            "course_id" => $course_id,
            "module_id" => $module_id,
            "test_id" => $test_id,
            "student_id" => $student_id
        ]);


        $monitoring = $this->db->get_where( TABLE_TEST_SETTINGS_MONITORING, [ 'test_id' => $test_id ] )->row();

        $data["monitoring"] = $monitoring;


        $submission_details = json_decode( $data["submission"]['details'], true);
        $problem_details_map = [];
        if (is_array($submission_details) && isset($submission_details['problem_scores']) ) {
            foreach ($submission_details['problem_scores']  as $detail) {
                $problem_details_map[$detail['problem_id']] = $detail;
            }
        }




        

        if (!$data["submission"]) {
            $this->session->set_flashdata("message", ["danger", "No submission found for this student."]);
            redirect(base_url($this->url . "/course/test_results/" . $course_id . "/" . $module_id . "/" . $test_id));
        }
        $total_time_spent = $this->db
        ->select_sum('time_spent')
        ->from('student_solutions')
        ->where([
            'course_id' => $course_id,
            'module_id' => $module_id,
            'test_id' => $test_id,
            'student_id' => $student_id
        ])
        ->get()
        ->row()
        ->time_spent;
        $data["submission"]["time_spent"] = $total_time_spent ? (int)$total_time_spent : 0;
        $data["submission"]["tab_changes"] = isset($data["submission"]["tab_changes"]) ? $data["submission"]["tab_changes"] : 0;

        // Get questions and student's answers
        $data["questions"] = $this->common->get_test_questions($test_id);

        // Test-level flag from UI settings
        $sections_enabled = $this->common->is_sections_enabled_for_test($test_id);

        $section_context = $this->common->build_section_context($data['questions'], $sections_enabled);
        $data['sections_enabled'] = $sections_enabled && $section_context['enabled'];
        $data['section_map'] = $section_context['map'];
        $data['questions_by_section'] = $section_context['grouped'];
        $data['section_order'] = $section_context['order'];

        // Preload all student's solutions for this test in a single query
        $solutions = $this->db_model->get_all("student_solutions", [
                "test_id" => $test_id,
                "student_id" => $student_id,
                "course_id" => $course_id,
                "module_id" => $module_id
            ]);

        $solutions_by_question = [];
        foreach ($solutions as $sol) {
            $qid = $sol['question_id'] ?? null;
            if ($qid !== null) {
                $solutions_by_question[$qid] = $sol;
            }
        }

        // Get student's answers for each question
        $data["student_solutions"] = [];
        foreach ($data["questions"] as $question) {
            // Get student's solution for this question from preloaded map
            $solution = $solutions_by_question[$question["question_id"]] ?? null;
            // var_dump( $solution);

            if ($solution) {
                // Process meta data
                $meta_data = json_decode($solution['meta'], true);
                $solution['submission_details'] = [
                    'ip_address' => $meta_data['userIp'] ?? 'N/A',
                    'browser' => $meta_data['userAgent'] ?? 'N/A',
                    'screen_resolution' => isset($meta_data['screenResolution']) ? 
                        $meta_data['screenResolution']['width'] . 'x' . $meta_data['screenResolution']['height'] : 'N/A',
                    'window_size' => isset($meta_data['windowResolution']) ? 
                        $meta_data['windowResolution']['width'] . 'x' . $meta_data['windowResolution']['height'] : 'N/A'
                ];

                if (isset($solution['problem_id']) && isset($problem_details_map[$solution['problem_id']])) {
                    $problem_detail = $problem_details_map[$solution['problem_id']];
                    $solution['submission_count'] = $problem_detail['submissions'] ?? 0;
                    $solution['problem_time_spent'] = $problem_detail['time_spent'] ?? 0;
                } else {
                    $solution['submission_count'] = 0;
                    $solution['problem_time_spent'] = 0;
                }

                // Process the solution based on question type
                if ($question['type'] == 1) { // MCQ
                    // Process MCQ solution
                    $solution_data = json_decode($solution['solution'], true);
                    
                    if ($solution_data !== null && is_array($solution_data)) {
                        // Get options from solution data and format them
                        $formatted_options = [];
                        
                        if (isset($solution_data['options']) && !empty($solution_data['options'])) {
                            // Process from solution_data
                            foreach ($solution_data['options'] as $option) {
                                $formatted_options[] = [
                                    'id' => $option['id'],
                                    'option_text' => $option['text'],
                                    'text' => $option['text'],
                                    'is_correct' => in_array($option['id'], $solution_data['correct_answer']) ? 1 : 0
                                ];
                            }
                        } else {
                            // Fetch from database
                            $options = $this->db_model->get_all("answer_options", [
                                "question_id" => $question["question_id"]
                            ]);
                            
                            foreach ($options as $option) {
                                $formatted_options[] = [
                                    'id' => $option['id'],
                                    'option_text' => $option['option_text'],
                                    'text' => $option['option_text'],
                                    'is_correct' => $option['is_correct']
                                ];
                            }
                        }
                        $solution['options'] = $formatted_options;
                        $solution['correct_answer'] = isset($solution_data['correct_answer']) ? $solution_data['correct_answer'] : [];
                        $solution['answered_options'] = [];
                        if (isset($solution_data['answer']) && is_array($solution_data['answer'])) {
                            foreach ($solution_data['answer'] as $answer_index) {
                                foreach ($formatted_options as $option) {
                                    if ($option['id'] == $answer_index) {
                                        $solution['answered_options'][] = $option;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    else {
                        $formatted_options = [];
                        $options = $this->db_model->get_all("answer_options", [
                            "question_id" => $question["question_id"]
                        ]);
                        
                        foreach ($options as $option) {
                            $formatted_options[] = [
                                'id' => $option['id'],
                                'option_text' => $option['option_text'],
                                'text' => $option['option_text'],
                                'is_correct' => $option['is_correct']
                            ];
                        }
                        $solution['options'] = $formatted_options;
                        $solution['answered_options'] = [];
                        $solution['correct_answer'] = [];
                    }
                    
                    $solution['max_score'] = $question['max_score'];
                }
                elseif ($question['type'] == 2) { // Code
                    // Get test cases
                    $test_cases = $this->db_model->get_all("question_test_cases", [
                        "question_id" => $question["question_id"]
                    ]);
                    
                    // Process solution and test cases
                    $solution_data = json_decode($solution['solution'], true);
                    // print_r($solution_data);
                    // exit;
                    if (is_array($solution_data)) {
                        // Extract code files from solution
                        $code_files = [];
                        foreach ($solution_data as $file) {
                            if (isset($file['name']) && isset($file['content'])) {
                                $code_files[] = [
                                    'name' => $file['name'],
                                    'content' => $file['content']
                                ];
                            }
                        }
                        $solution['code_solution'] = $code_files;
                        // ✅ Add plagiarism_score if available
                        $solution['plagiarism_score'] = isset($solution_data['plagiarism_score'])  ? $solution_data['plagiarism_score'] : ''; 
                    } else {
                        $solution['code_solution'] = [];
                    }
                    
                    // Process solution data for test cases
                    if (isset($solution_data['testcase_result'])) {
                        $solution['test_cases'] = $solution_data['testcase_result'];
                    } else {
                        $solution['test_cases'] = $test_cases;
                    }
                    // Add language information
                    $solution['language'] = $solution['language'] ?? 'N/A';
                    
                    // Add max score
                    $solution['max_score'] = $question['max_score'];
                }
                elseif ($question['type'] == 3) { // Fill in Blank
                    $solution['answered_text'] = $solution['solution'];
                    $solution['correct_answer'] = $question['fill_blank_answer'];
                }

                // Add time spent
                $solution['time_spent'] = $solution['time_spent'] ?? 0;
                $solution['formatted_time_spent'] = $this->format_time_spent($solution['time_spent']);

                $data["student_solutions"][$question["question_id"]] = $solution;
            } else {
                // Create empty solution structure
                $empty_solution = [
                    'score' => 0,
                    'max_score' => $question['max_score'],
                    'time_spent' => 0,
                    'formatted_time_spent' => '0s',
                    'submission_details' => [
                        'ip_address' => 'N/A',
                        'browser' => 'N/A',
                        'screen_resolution' => 'N/A',
                        'window_size' => 'N/A'
                    ]
                ];

                // Add type-specific fields
                if ($question['type'] == 1) {
                    $options = $this->db_model->get_all("answer_options", [
                        "question_id" => $question["question_id"]
                    ]);
                    $empty_solution['options'] = $options;
                    $empty_solution['answered_options'] = [];
                }
                elseif ($question['type'] == 2) {
                    $test_cases = $this->db_model->get_all("question_test_cases", [
                        "question_id" => $question["question_id"]
                    ]);
                    $empty_solution['test_cases'] = $test_cases;
                    $empty_solution['code_solution'] = [];
                    $empty_solution['language'] = 'N/A';
                }
                elseif ($question['type'] == 3) {
                    $empty_solution['correct_answer'] = $question['fill_blank_answer'];
                    $empty_solution['answered_text'] = '';
                }

                $data["student_solutions"][$question["question_id"]] = $empty_solution;
            
            }
        }

        
        // Get difficulty levels for badges
        $data["difficulty_map"] = [
            1 => "Easy",
            2 => "Medium",
            3 => "Hard"
        ];


        
        

        $this->load->view("faculty/faculty/sidebar", $class);
        $this->load->view("course/student_test_report", $data);
        $this->load->view("faculty/faculty/footer");
    }

    // all_course_student_test_report


    public function all_course_student_test_report($course_id = null, $module_id = null, $test_id = null, $student_id = null) {
        if (!$course_id || !$module_id || !$test_id || !$student_id) {
            redirect(base_url($this->url . "/course"));
        }

        $data["url"] = $this->url;
        $class["classname"] = "courses";
        $data['route'] = 'allcourses';
        $class["url"] = $this->url;
        $map = [
            DESIGNATION_STAFF => "staff",
            DESIGNATION_PRINCIPAL => "principal",
        ];
        $path = $map[$this->session_data["designation"]] ?? "hod";
        $class["sidebar_href"] = base_url($this->url . "/" . $path);
        $data["college_id"] = $this->college["id"];

        // Get course details
        // $data["course"] = $this->db_model->get_row(TABLE_COURCES, [
        //     "id" => $course_id,
        //     "is_active" => 1,
        //     "college_id" => $this->college["id"],
        // ]);
         // Modified: Fetch course from either direct or shared (special) mapping
            
        $data["course"] = $this->common->get_course_details($course_id, $this->college['id']);;

        if (!$data["course"]) {
            $this->session->set_flashdata("message", ["danger", "Course not found."]);
            redirect(base_url($this->url . "/course"));
        }

        // Get module details
        $data["module"] = $this->db_model->get_row("course_modules", [
            "id" => $module_id,
            "course_id" => $course_id,
            "is_active" => 1,
        ]);

        if (!$data["module"]) {
            $this->session->set_flashdata("message", ["danger", "Module not found."]);
            redirect(base_url($this->url . "/course/modules/" . $course_id));
        }

        // Get test details
        $data["test"] = $this->db
            ->select("t.*, ct.start_date, ct.end_date")
            ->from("tests t")
            ->join("course_tests ct", "ct.test_id = t.id")
            ->where("t.id", $test_id)
            ->where("ct.course_id", $course_id)
            ->where("ct.module_id", $module_id)
            ->where("ct.is_active", 1)
            ->get()
            ->row_array();

        if (!$data["test"]) {
            $this->session->set_flashdata("message", ["danger", "Test not found."]);
            redirect(base_url($this->url . "/course/module_tests/" . $course_id . "/" . $module_id));
        }

        // Get student details
        $data["student"] = $this->db
            ->select("s.*, d.name as department_name, s.registration_number")
            ->from("students s")
            ->join("departments d", "d.id = s.department")
            ->where("s.id", $student_id)
            ->where("s.is_active", 1)
            ->get()
            ->row_array();

        if (!$data["student"]) {
            $this->session->set_flashdata("message", ["danger", "Student not found."]);
            redirect(base_url($this->url . "/course/test_results/" . $course_id . "/" . $module_id . "/" . $test_id));
        }

        // Get student's submission
        $data["submission"] = $this->db_model->get_row("student_test_submission", [
            "course_id" => $course_id,
            "module_id" => $module_id,
            "test_id" => $test_id,
            "student_id" => $student_id
        ]);


        $submission_details = json_decode( $data["submission"]['details'], true);
        $problem_details_map = [];
        if (is_array($submission_details) && isset($submission_details['problem_scores'])) {
            foreach ($submission_details['problem_scores'] as $detail) {
                $problem_details_map[$detail['problem_id']] = $detail;
            }
        }
        
        
        $monitoring = $this->db->get_where( TABLE_TEST_SETTINGS_MONITORING, [ 'test_id' => $test_id ] )->row();

        $data["monitoring"] = $monitoring;

        // var_dump( $data["monitoring"]); die;

        

        if (!$data["submission"]) {
            $this->session->set_flashdata("message", ["danger", "No submission found for this student."]);
            redirect(base_url($this->url . "/course/test_results/" . $course_id . "/" . $module_id . "/" . $test_id));
        }
        $total_time_spent = $this->db
        ->select_sum('time_spent')
        ->from('student_solutions')
        ->where([
            'course_id' => $course_id,
            'module_id' => $module_id,
            'test_id' => $test_id,
            'student_id' => $student_id
        ])
        ->get()
        ->row()
        ->time_spent;
        $data["submission"]["time_spent"] = $total_time_spent ? (int)$total_time_spent : 0;
        $data["submission"]["tab_changes"] = isset($data["submission"]["tab_changes"]) ? $data["submission"]["tab_changes"] : 0;

        // Get questions and student's answers
        $data["questions"] = $this->common->get_test_questions($test_id);

        // Test-level flag from UI settings
        $sections_enabled = $this->common->is_sections_enabled_for_test($test_id);

        $section_context = $this->common->build_section_context($data['questions'], $sections_enabled);
        $data['sections_enabled'] = $sections_enabled && $section_context['enabled'];
        $data['section_map'] = $section_context['map'];
        $data['questions_by_section'] = $section_context['grouped'];
        $data['section_order'] = $section_context['order'];


        // Preload all student's solutions for this test in a single query
        $solutions = $this->db_model->get_all("student_solutions", [
                "test_id" => $test_id,
                "student_id" => $student_id,
                "course_id" => $course_id,
                "module_id" => $module_id
            ]);

        $solutions_by_question = [];
        foreach ($solutions as $sol) {
            $qid = $sol['question_id'] ?? null;
            if ($qid !== null) {
                $solutions_by_question[$qid] = $sol;
            }
        }

        // Get student's answers for each question
        $data["student_solutions"] = [];
        foreach ($data["questions"] as $question) {
            // Get student's solution for this question from preloaded map
            $solution = $solutions_by_question[$question["question_id"]] ?? null;
            // var_dump( $solution);

            if ($solution) {
                // Process meta data
                $meta_data = json_decode($solution['meta'], true);
                $solution['submission_details'] = [
                    'ip_address' => $meta_data['userIp'] ?? 'N/A',
                    'browser' => $meta_data['userAgent'] ?? 'N/A',
                    'screen_resolution' => isset($meta_data['screenResolution']) ? 
                        $meta_data['screenResolution']['width'] . 'x' . $meta_data['screenResolution']['height'] : 'N/A',
                    'window_size' => isset($meta_data['windowResolution']) ? 
                        $meta_data['windowResolution']['width'] . 'x' . $meta_data['windowResolution']['height'] : 'N/A'
                ];


                if (isset($solution['problem_id']) && isset($problem_details_map[$solution['problem_id']])) {
                    $problem_detail = $problem_details_map[$solution['problem_id']];
                    $solution['submission_count'] = $problem_detail['submissions'] ?? 0;
                    $solution['problem_time_spent'] = $problem_detail['time_spent'] ?? 0;
                } else {
                    $solution['submission_count'] = 0;
                    $solution['problem_time_spent'] = 0;
                }
                
                // Process the solution based on question type
                if ($question['type'] == 1) { // MCQ
                    $solution_data = json_decode($solution['solution'], true);
                    
                    if ($solution_data !== null && is_array($solution_data)) {
                        $formatted_options = [];
                        
                        if (isset($solution_data['options']) && !empty($solution_data['options'])) {
                            // Process from solution_data
                            foreach ($solution_data['options'] as $option) {
                                $formatted_options[] = [
                                    'id' => $option['id'],
                                    'option_text' => $option['text'],
                                    'text' => $option['text'],
                                    'is_correct' => in_array($option['id'], $solution_data['correct_answer']) ? 1 : 0
                                ];
                            }
                        } else {
                            // Fetch from database if options not in solution_data
                            $options = $this->db_model->get_all("answer_options", [
                                "question_id" => $question["question_id"]
                            ]);
                            
                            foreach ($options as $option) {
                                $formatted_options[] = [
                                    'id' => $option['id'],
                                    'option_text' => $option['option_text'],
                                    'text' => $option['option_text'],
                                    'is_correct' => $option['is_correct']
                                ];
                            }
                        }
                        $solution['options'] = $formatted_options;
                        
                       
                        $solution['correct_answer'] = isset($solution_data['correct_answer'])  ? $solution_data['correct_answer'] : [];
                                   
                        $solution['answered_options'] = [];
                        if (isset($solution_data['answer']) && is_array($solution_data['answer'])) {
                            foreach ($solution_data['answer'] as $answer_index) {
                                foreach ($formatted_options as $option) {
                                    if ($option['id'] == $answer_index) {
                                        $solution['answered_options'][] = $option;
                                        break;
                                    }
                                }
                            }
                        }
                    } else {
                        // ✅ Handle completely invalid solution_data
                        $formatted_options = [];
                        $options = $this->db_model->get_all("answer_options", [
                            "question_id" => $question["question_id"]
                        ]);
                        
                        foreach ($options as $option) {
                            $formatted_options[] = [
                                'id' => $option['id'],
                                'option_text' => $option['option_text'],
                                'text' => $option['option_text'],
                                'is_correct' => $option['is_correct']
                            ];
                        }
                        
                        $solution['options'] = $formatted_options;
                        $solution['answered_options'] = [];
                        $solution['correct_answer'] = [];
                    }
                    
                    $solution['max_score'] = $question['max_score'];
                }
                elseif ($question['type'] == 2) { // Code
                    // Get test cases
                    $test_cases = $this->db_model->get_all("question_test_cases", [
                        "question_id" => $question["question_id"]
                    ]);
                    
                    // Process solution and test cases
                    $solution_data = json_decode($solution['solution'], true);
                    if (is_array($solution_data)) {
                        // Extract code files from solution
                        $code_files = [];
                        foreach ($solution_data as $file) {
                            if (isset($file['name']) && isset($file['content'])) {
                                $code_files[] = [
                                    'name' => $file['name'],
                                    'content' => $file['content']
                                ];
                            }
                        }
                        $solution['code_solution'] = $code_files;
                    } else {
                        $solution['code_solution'] = [];
                    }
                    
                    // Process solution data for test cases
                    if (isset($solution_data['testcase_result'])) {
                        $solution['test_cases'] = $solution_data['testcase_result'];
                    } else {
                        $solution['test_cases'] = $test_cases;
                    }
                    // 🔥 Add Pass/Fail status if missing
                    foreach ($solution['test_cases'] as &$tc) {
                        // student_output should come from execution logs (meta_data) or stored results
                        $student_output = $tc['student_output'] ?? null;

                        if ($student_output !== null && trim($student_output) == trim($tc['output'])) {
                            $tc['status'] = "PASS";
                        } else {
                            $tc['status'] = "FAIL";
                        }
                    }

                    unset($tc); // break reference
                    // Add language information
                    $solution['language'] = $solution['language'] ?? 'N/A';
                    
                    // Add max score
                    $solution['max_score'] = $question['max_score'];
                }
                elseif ($question['type'] == 3) { // Fill in Blank
                    $solution['answered_text'] = $solution['solution'];
                    $solution['correct_answer'] = $question['fill_blank_answer'];
                }

                // Add time spent
                $solution['time_spent'] = $solution['time_spent'] ?? 0;
                $solution['formatted_time_spent'] = $this->format_time_spent($solution['time_spent']);

                $data["student_solutions"][$question["question_id"]] = $solution;
            } else {
                // Create empty solution structure
                $empty_solution = [
                    'score' => 0,
                    'max_score' => $question['max_score'],
                    'time_spent' => 0,
                    'formatted_time_spent' => '0s',
                    'submission_details' => [
                        'ip_address' => 'N/A',
                        'browser' => 'N/A',
                        'screen_resolution' => 'N/A',
                        'window_size' => 'N/A'
                    ]
                ];

                // Add type-specific fields
                if ($question['type'] == 1) {
                    $options = $this->db_model->get_all("answer_options", [
                        "question_id" => $question["question_id"]
                    ]);
                    $empty_solution['options'] = $options;
                    $empty_solution['answered_options'] = [];
                }
                elseif ($question['type'] == 2) {
                    $test_cases = $this->db_model->get_all("question_test_cases", [
                        "question_id" => $question["question_id"]
                    ]);
                    $empty_solution['test_cases'] = $test_cases;
                    $empty_solution['code_solution'] = [];
                    $empty_solution['language'] = 'N/A';
                }
                elseif ($question['type'] == 3) {
                    $empty_solution['correct_answer'] = $question['fill_blank_answer'];
                    $empty_solution['answered_text'] = '';
                }

                $data["student_solutions"][$question["question_id"]] = $empty_solution;
            }
        }

        
        // Get difficulty levels for badges
        $data["difficulty_map"] = [
            1 => "Easy",
            2 => "Medium",
            3 => "Hard"
        ];

        $this->load->view("faculty/faculty/sidebar", $class);
        $this->load->view("course/student_test_report", $data);
        $this->load->view("faculty/faculty/footer");
    }

    public function student_overall_test_report($student_id = null)
    {
        
        $data["url"] = $this->url;
        $class["classname"] = "courses";
        $class["url"] = $this->url;
        $map = [
            DESIGNATION_STAFF => "staff",
            DESIGNATION_PRINCIPAL => "principal",
        ];
        
        $path = $map[$this->session_data["designation"]] ?? "hod";
        $class["sidebar_href"] = base_url($this->url . "/" . $path);
        $data["college_id"] = $this->college["id"];

        if (!$student_id) {
            $this->session->set_flashdata("message", ["danger", "Invalid student ID."]);
            redirect(base_url($this->url . "/" . $path. "/students"));
        }


        // Get student profile
        $data["student"] = $this->db
            ->select("s.*, d.name as department_name")
            ->from("students s")
            ->join("departments d", "d.id = s.department", "left")
            ->where("s.id", $student_id)
            ->where("s.college_id",$this->college['id'])
            ->where("s.is_active", 1)
            ->get()
            ->row_array();

        if (!$data["student"]) {
            $this->session->set_flashdata("message", ["danger", "Student not found."]);
            redirect(base_url($this->url . "/" . $path. "/students"));
        }

        // Get all submissions for student
        $data["submission"] = $this->db
        ->select("
            sts.*,
            c.id as course_id,
            c.name as course_name,
            m.id as module_id,
            m.name as module_name,
            t.id as test_id,
            t.title as test_title
        ")
        ->from("student_test_submission sts")
        ->join("courses c", "c.id = sts.course_id", "left")
        ->join("course_modules m", "m.id = sts.module_id", "left")
        ->join("tests t", "t.id = sts.test_id", "left")
        ->where("sts.student_id", $student_id)
        ->order_by("sts.created_at", "desc")
        ->get()
        ->result_array();

        // Prepare pass/fail status
        foreach ($data["submission"] as &$submission) {
            $submission['status'] = ($submission['total_score'] >= $submission['earned_score']) ? 'Pass' : 'Fail';
            
            // Generate detail link to the existing route
            $submission['detail_url'] = base_url($this->url . "/allcourses/student_test_report/" .
                $submission['course_id'] . "/" .
                $submission['module_id'] . "/" .
                $submission['test_id'] . "/" .
                $student_id
            );
        }
        // print_r($data);
        // exit;
        // Load view
        $this->load->view("faculty/faculty/sidebar", $class);
        $this->load->view("course/student_overall_test_report", $data);
        $this->load->view("faculty/faculty/footer");
    }

    public function export_test_report($course_id = null, $module_id = null, $test_id = null, $student_id = null) {
        if (!$course_id || !$module_id || !$test_id || !$student_id) {
            redirect(base_url($this->url . "/course"));
        }

        // Get course details
        $course = $this->db_model->get_row(TABLE_COURCES, [
            "id" => $course_id,
            "is_active" => 1,
            "college_id" => $this->college["id"],
        ]);

        if (!$course) {
            $this->session->set_flashdata("message", ["danger", "Course not found."]);
            redirect(base_url($this->url . "/course"));
        }

        // Get module details
        $module = $this->db_model->get_row("course_modules", [
            "id" => $module_id,
            "course_id" => $course_id,
            "is_active" => 1,
        ]);

        if (!$module) {
            $this->session->set_flashdata("message", ["danger", "Module not found."]);
            redirect(base_url($this->url . "/course/modules/" . $course_id));
        }

        // Get test details
        $test = $this->db
            ->select("t.*, ct.start_date, ct.end_date")
            ->from("tests t")
            ->join("course_tests ct", "ct.test_id = t.id")
            ->where("t.id", $test_id)
            ->where("ct.course_id", $course_id)
            ->where("ct.module_id", $module_id)
            ->where("ct.is_active", 1)
            ->get()
            ->row_array();

        if (!$test) {
            $this->session->set_flashdata("message", ["danger", "Test not found."]);
            redirect(base_url($this->url . "/course/module_tests/" . $course_id . "/" . $module_id));
        }

        // Get student details
        $student = $this->db
            ->select("s.*, d.name as department_name")
            ->from("students s")
            ->join("departments d", "d.id = s.department")
            ->where("s.id", $student_id)
            ->where("s.is_active", 1)
            ->get()
            ->row_array();

        if (!$student) {
            $this->session->set_flashdata("message", ["danger", "Student not found."]);
            redirect(base_url($this->url . "/course/test_results/" . $course_id . "/" . $module_id . "/" . $test_id));
        }

        // Get student's submission
        $submission = $this->db_model->get_row("student_test_submission", [
            "course_id" => $course_id,
            "module_id" => $module_id,
            "test_id" => $test_id,
            "student_id" => $student_id
        ]);

        if (!$submission) {
            $this->session->set_flashdata("message", ["danger", "No submission found for this student."]);
            redirect(base_url($this->url . "/course/test_results/" . $course_id . "/" . $module_id . "/" . $test_id));
        }

        // Ensure time_spent and tab_changes are set with default values
        $submission['time_spent'] = isset($submission['time_spent']) ? $submission['time_spent'] : 0;
        $submission['tab_changes'] = isset($submission['tab_changes']) ? $submission['tab_changes'] : 0;

        // Get questions and student's answers
        // Check if sections are enabled
        $this->db->select('*');
        $this->db->from(TABLE_TEST_SETTINGS_UI);
        $this->db->where('test_id', $test_id);
        $ui = $this->db->get()->row();
        $sections_enabled = $ui && $ui->enable_sections;
        
        // If sections are enabled, exclude questions in the default unassigned section
        $default_section_id = null;
        if ($sections_enabled) {
            $this->db->select('*');
            $this->db->from(TABLE_TEST_SECTIONS);
            $this->db->where('id', DEFAULT_SECTION_ID);
            $default_section = $this->db->get()->row();
            
            if ($default_section) {
                $default_section_id = $default_section->id;
            }
        }
        
        $this->db->select("q.*, tq.question_order, tq.section_id, q.score as max_score, q.id as question_id, q.question_title, q.question_content, q.type, q.fill_blank_answer");
        $this->db->from("test_questions tq");
        $this->db->join("question_bank q", "q.id = tq.question_id");
        $this->db->where("tq.test_id", $test_id);
        $this->db->where("tq.is_active", 1);
        
        if ($default_section_id !== null) {
            $this->db->where("tq.section_id !=", $default_section_id);
        }
        
        $questions = $this->db->get()->result_array();

        // Get student's solutions
        $student_solutions = [];
        foreach ($questions as $question) {
            $solution = $this->db_model->get_row("student_solutions", [
                "test_id" => $test_id,
                "question_id" => $question["question_id"],
                "student_id" => $student_id,
                "course_id" => $course_id,
                "module_id" => $module_id
            ]);

            if ($solution) {
                $student_solutions[$question["question_id"]] = $solution;
            }
        }

        // Prepare HTML report
        $filename = "test_report_{$student['name']}_{$test['title']}.html";
        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Start HTML report
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Test Report</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 1200px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    padding: 20px;
                    background: #f8f9fa;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }
                .section {
                    margin-bottom: 30px;
                    padding: 20px;
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }
                .section-title {
                    color: #2c3e50;
                    border-bottom: 2px solid #3498db;
                    padding-bottom: 10px;
                    margin-bottom: 20px;
                }
                .info-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                    gap: 20px;
                    margin-bottom: 20px;
                }
                .info-item {
                    background: #f8f9fa;
                    padding: 15px;
                    border-radius: 6px;
                }
                .info-label {
                    font-weight: bold;
                    color: #666;
                    margin-bottom: 5px;
                }
                .info-value {
                    color: #2c3e50;
                    font-size: 1.1em;
                }
                .result-badge {
                    display: inline-block;
                    padding: 5px 15px;
                    border-radius: 20px;
                    font-weight: bold;
                    color: white;
                }
                .pass {
                    background-color: #2ecc71;
                }
                .fail {
                    background-color: #e74c3c;
                }
                .question-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
                .question-table th {
                    background: #3498db;
                    color: white;
                    padding: 12px;
                    text-align: left;
                }
                .question-table td {
                    padding: 12px;
                    border-bottom: 1px solid #ddd;
                }
                .question-table tr:nth-child(even) {
                    background: #f8f9fa;
                }
                .question-table tr:hover {
                    background: #f1f1f1;
                }
                .score {
                    font-weight: bold;
                }
                .attempted {
                    color: #2ecc71;
                }
                .not-attempted {
                    color: #e74c3c;
                }
                .progress-bar {
                    width: 100%;
                    height: 20px;
                    background: #f0f0f0;
                    border-radius: 10px;
                    overflow: hidden;
                    margin-top: 5px;
                }
                .progress-fill {
                    height: 100%;
                    background: #3498db;
                    transition: width 0.3s ease;
                }
                .stats-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 20px;
                    margin-bottom: 30px;
                }
                .stat-card {
                    background: white;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    text-align: center;
                }
                .stat-value {
                    font-size: 24px;
                    font-weight: bold;
                    color: #3498db;
                    margin: 10px 0;
                }
                .stat-label {
                    color: #666;
                    font-size: 14px;
                }
            </style>
        </head>
        <body>';

        // Header
        echo '<div class="header">';
        echo '<h1>Test Report</h1>';
        echo '<p>' . htmlspecialchars($test['title']) . '</p>';
        echo '</div>';

        // Student Information Section
        echo '<div class="section">';
        echo '<h2 class="section-title">Student Information</h2>';
        echo '<div class="info-grid">';
        echo '<div class="info-item">';
        echo '<div class="info-label">Student Name</div>';
        echo '<div class="info-value">' . htmlspecialchars($student['name']) . '</div>';
        echo '</div>';
        echo '<div class="info-item">';
        echo '<div class="info-label">Email</div>';
        echo '<div class="info-value">' . htmlspecialchars($student['email']) . '</div>';
        echo '</div>';
        echo '<div class="info-item">';
        echo '<div class="info-label">Department</div>';
        echo '<div class="info-value">' . htmlspecialchars($student['department_name']) . '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        // Test Results Section
        echo '<div class="section">';
        echo '<h2 class="section-title">Test Results</h2>';
        
        // Stats Grid
        echo '<div class="stats-grid">';
        echo '<div class="stat-card">';
        echo '<div class="stat-label">Score</div>';
        echo '<div class="stat-value">' . $submission['percentage'] . '%</div>';
        echo '<div class="progress-bar"><div class="progress-fill" style="width: ' . $submission['percentage'] . '%"></div></div>';
        echo '</div>';
        
        echo '<div class="stat-card">';
        echo '<div class="stat-label">Points Earned</div>';
        echo '<div class="stat-value">' . $submission['earned_score'] . '/' . $submission['total_score'] . '</div>';
        echo '</div>';
        
        echo '<div class="stat-card">';
        echo '<div class="stat-label">Result</div>';
        $result_class = $submission['percentage'] >= $test['pass_percentage'] ? 'pass' : 'fail';
        echo '<div class="stat-value"><span class="result-badge ' . $result_class . '">' . 
            ($submission['percentage'] >= $test['pass_percentage'] ? 'Pass' : 'Fail') . '</span></div>';
        echo '</div>';
        
        echo '<div class="stat-card">';
        echo '<div class="stat-label">Time Spent</div>';
        echo '<div class="stat-value">' . $this->format_time_spent($submission['time_spent']) . '</div>';
        echo '</div>';
        
        echo '<div class="stat-card">';
        echo '<div class="stat-label">Tab Changes</div>';
        echo '<div class="stat-value">' . $submission['tab_changes'] . '</div>';
        echo '</div>';
        echo '</div>';

        // Test Information
        echo '<div class="info-grid">';
        echo '<div class="info-item">';
        echo '<div class="info-label">Test Title</div>';
        echo '<div class="info-value">' . htmlspecialchars($test['title']) . '</div>';
        echo '</div>';
        echo '<div class="info-item">';
        echo '<div class="info-label">Module</div>';
        echo '<div class="info-value">' . htmlspecialchars($module['name']) . '</div>';
        echo '</div>';
        echo '<div class="info-item">';
        echo '<div class="info-label">Course</div>';
        echo '<div class="info-value">' . htmlspecialchars($course['name']) . '</div>';
        echo '</div>';
        echo '<div class="info-item">';
        echo '<div class="info-label">Submission Date</div>';
        echo '<div class="info-value">' . date('Y-m-d H:i:s', strtotime($submission['submission_time'])) . '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        // Question Analysis Section
        echo '<div class="section">';
        echo '<h2 class="section-title">Question Analysis</h2>';
        echo '<table class="question-table">';
        echo '<tr>';
        echo '<th>Question</th>';
        echo '<th>Title</th>';
        echo '<th>Score</th>';
        echo '<th>Time Spent</th>';
        echo '<th>IP Address</th>';
        echo '<th>Browser</th>';
        echo '<th>Screen Resolution</th>';
        echo '<th>Status</th>';
        echo '</tr>';
        
        foreach ($questions as $question) {
            $solution = isset($student_solutions[$question['question_id']]) ? $student_solutions[$question['question_id']] : null;
            $meta_data = $solution ? json_decode($solution['meta'], true) : null;
            
            echo '<tr>';
            echo '<td>Question ' . $question['question_order'] . '</td>';
            echo '<td>' . htmlspecialchars($question['question_title']) . '</td>';
            echo '<td class="score">' . ($solution ? ($solution['score'] . '/' . $question['max_score']) : 'Not Attempted') . '</td>';
            echo '<td>' . ($solution ? $this->format_time_spent($solution['time_spent']) : 'N/A') . '</td>';
            echo '<td>' . ($meta_data ? htmlspecialchars($meta_data['userIp'] ?? 'N/A') : 'N/A') . '</td>';
            echo '<td>' . ($meta_data ? htmlspecialchars($meta_data['userAgent'] ?? 'N/A') : 'N/A') . '</td>';
            echo '<td>' . (isset($meta_data['screenResolution']) ? 
                ($meta_data['screenResolution']['width'] . 'x' . $meta_data['screenResolution']['height']) : 'N/A') . '</td>';
            echo '<td class="' . ($solution ? 'attempted' : 'not-attempted') . '">' . 
                ($solution ? 'Attempted' : 'Not Attempted') . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';

        echo '</body></html>';
        exit;
    }

    public function export_test_report_csv($course_id = null, $module_id = null, $test_id = null, $student_id = null) {
        if (!$course_id || !$module_id || !$test_id || !$student_id) {
            redirect(base_url($this->url . "/course"));
        }

        // Get student details
        $student = $this->db_model->get_row(TABLE_STUDENT, ['id' => $student_id]);
        if (!$student) {
            $this->session->set_flashdata('message', array('danger', 'Student not found'));
            redirect($this->url . '/course');
        }

        // Get test details
        $test = $this->db_model->get_row(TABLE_TESTS, ['id' => $test_id]);
        if (!$test) {
            $this->session->set_flashdata('message', array('danger', 'Test not found'));
            redirect($this->url . '/course');
        }

        // Get module details
        $module = $this->db_model->get_row("course_modules", ['id' => $module_id]);
        if (!$module) {
            $this->session->set_flashdata('message', array('danger', 'Module not found'));
            redirect($this->url . '/course');
        }

        // Get course details
        $course = $this->db_model->get_row(TABLE_COURCES, ['id' => $course_id]);
        if (!$course) {
            $this->session->set_flashdata('message', array('danger', 'Course not found'));
            redirect($this->url . '/course');
        }

        // Get test submission
        $submission = $this->db_model->get_row("student_test_submission", [
            "test_id" => $test_id,
            "student_id" => $student_id,
            "course_id" => $course_id,
            "module_id" => $module_id
        ]);

        $total_time_spent = $this->db
        ->select_sum('time_spent')
        ->from('student_solutions')
        ->where([
            'course_id' => $course_id,
            'module_id' => $module_id,
            'test_id' => $test_id,
            'student_id' => $student_id
        ])
        ->get()
        ->row()
        ->time_spent;
        $total_hours = floor($total_time_spent / 3600);
        $total_minutes = floor(($total_time_spent % 3600) / 60);
        $total_seconds = $total_time_spent % 60;
        
        $total_time_display = '';
        if ($total_hours > 0) {
            $total_time_display .= $total_hours . 'h ';
        }
        if ($total_minutes > 0 || $total_hours > 0) {
            $total_time_display .= $total_minutes . 'm ';
        }
        $total_time_display .= $total_seconds . 's';
        $submission['time_spent'] = $total_time_display;

        if (!$submission) {
            $this->session->set_flashdata('message', array('danger', 'Test submission not found'));
            redirect($this->url . '/course');
        }

        // Get questions with all related data
        $questions = $this->common->get_test_questions($test_id);
        // Test-level flag from UI settings
        $sections_enabled = $this->common->is_sections_enabled_for_test($test_id);

        $section_context = $this->common->build_section_context($questions, $sections_enabled);
        $section_map = $section_context['map'];
        $questions_by_section = $section_context['grouped'];
        $section_order = $section_context['order'];
        
        // Get student's solutions in a single query and index by question_id
        $student_solutions = [];
        $solutions = $this->db
            ->select('*')
            ->from('student_solutions')
            ->where('test_id', $test_id)
            ->where('course_id', $course_id)
            ->where('module_id', $module_id)
            ->where('student_id', $student_id)
            ->get()
            ->result_array();

        foreach ($solutions as $solution) {
            $qid = $solution['question_id'] ?? null;
            if ($qid !== null) {
                $student_solutions[$qid] = $solution;
            }
        }

        // Prepare CSV data
        $filename = "test_report_{$student['name']}_{$test['title']}.csv";
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Add UTF-8 BOM to ensure proper character encoding
        fwrite($output, "\xEF\xBB\xBF");

        // Write headers
        fputcsv($output, ['Test Report']);
        fputcsv($output, []);

        // Student Information
        fputcsv($output, ['Student Information']);
        fputcsv($output, ['Name', $student['name']]);
        fputcsv($output, ['Email', $student['email']]);
        fputcsv($output, ['Registration Number', $student['registration_number']]);
        fputcsv($output, ['Department', $this->db_model->get_row(TABLE_DEPARTMENT, ['id' => $student['department']])['name']]);
        fputcsv($output, []);

        // Test Information
        fputcsv($output, ['Test Information']);
        fputcsv($output, ['Test Title', $test['title']]);
        fputcsv($output, ['Module', $module['name']]);
        fputcsv($output, ['Course', $course['name']]);
        fputcsv($output, ['Submission Date', $submission['submission_time']]);
        fputcsv($output, []);




    

        // Test Results
        fputcsv($output, ['Test Results']);
        fputcsv($output, ['Score', number_format($submission['percentage'], 2) . '%']);
        // Prefix with a tab to prevent Excel auto-formatting as dates
        $points_earned_text = "\t" . ($submission['earned_score'] . '/' . $submission['total_score']);
        fputcsv($output, ['Points Earned', $points_earned_text]);
        fputcsv($output, ['Result', $submission['percentage'] >= $test['pass_percentage'] ? 'Pass' : 'Fail']);
        fputcsv($output, ['Time Spent', $submission['time_spent'] ?? '0s']);
        fputcsv($output, ['Tab Changes', $submission['tab_changes'] ?? '0']);
        // ✅ Plagiarism Score (only for coding problems)
        $plagiarism_score = '-';
        if (!empty($submission['details'])) {
            $details = json_decode($submission['details'], true);
            if (!empty($details['problem_scores'])) {
                foreach ($details['problem_scores'] as $problem) {
                    if (($problem['problem_type'] ?? '') === 'code' && isset($problem['plagiarism_score'])) {
                        $plagiarism_score = $problem['plagiarism_score'] . '%';
                        break; // just take first code problem
                    }
                }
            }
        }
        fputcsv($output, ['Plagiarism Score', $plagiarism_score]);

        fputcsv($output, []);

        // Question Analysis
        fputcsv($output, ['Question Analysis']);
        $write_question_row = function ($question, $section_label = null) use (&$output, $student_solutions) {
            $solution = isset($student_solutions[$question['question_id']]) ? $student_solutions[$question['question_id']] : null;
            $meta_data = ($solution && !empty($solution['meta'])) ? json_decode($solution['meta'], true) : null;

            $problem_time_spend = ($solution && isset($solution['time_spent'])) ? (int)$solution['time_spent'] : 0;
            $total_hours = floor($problem_time_spend / 3600);
            $total_minutes = floor(( $problem_time_spend % 3600) / 60);
            $total_seconds =$problem_time_spend % 60;
            $total_time_display = '';
            if ($total_hours > 0) {
                $total_time_display .= $total_hours . 'h ';
            }
            if ($total_minutes > 0 || $total_hours > 0) {
                $total_time_display .= $total_minutes . 'm ';
            }
            $total_time_display .= $total_seconds . 's';
            $score_str = "\t" . (($solution['score'] ?? 0) . '/' . ($question['max_score'] ?? 0));
            $time_spent_str = $solution ? $total_time_display : '0s';
            $ip_str = $meta_data ? ($meta_data['userIp'] ?? 'N/A') : 'N/A';
            $ua_str = $meta_data ? ($meta_data['userAgent'] ?? 'N/A') : 'N/A';
            $sr_str = (isset($meta_data['screenResolution'])) ?
                ($meta_data['screenResolution']['width'] . 'x' . $meta_data['screenResolution']['height']) : 'N/A';
            $status_str = $solution ? 'Attempted' : 'Not Attempted';

            $row = [];
            if ($section_label !== null) {
                $row[] = $section_label;
            }
            $row[] = 'Question ' . ($question['question_order'] ?? '');
            $row[] = $question['question_title'] ?? '';
            $row[] = $score_str;
            $row[] = $time_spent_str;
            $row[] = $ip_str;
            $row[] = $ua_str;
            $row[] = $sr_str;
            $row[] = $status_str;

            fputcsv($output, $row);
        };

        if ($sections_enabled && !empty($questions_by_section)) {
            fputcsv($output, ['Section', 'Question', 'Title', 'Score', 'Time Spent', 'IP Address', 'Browser', 'Screen Resolution', 'Status']);
            $ordered_sections = !empty($section_order) ? $section_order : array_keys($questions_by_section);
            foreach ($ordered_sections as $section_key) {
                if (!isset($questions_by_section[$section_key])) {
                    continue;
                }

                $section_name = isset($section_map[$section_key])
                    ? $section_map[$section_key]
                    : (is_numeric($section_key) ? 'Section ' . $section_key : 'Unassigned Questions');

                foreach ($questions_by_section[$section_key] as $question) {
                    $write_question_row($question, $section_name);
                }
            }
        } else {
            fputcsv($output, ['Question', 'Title', 'Score', 'Time Spent', 'IP Address', 'Browser', 'Screen Resolution', 'Status']);
            foreach ($questions as $question) {
                $write_question_row($question);
            }
        }

        fclose($output);
    }

    public function export_all_test_results($course_id = null, $module_id = null, $test_id = null) {
        if (!$course_id || !$module_id || !$test_id) {
            redirect(base_url($this->url . "/course"));
        }

        // Get test details
        $test = $this->db
            ->select("t.*, ct.start_date, ct.end_date")
            ->from("tests t")
            ->join("course_tests ct", "ct.test_id = t.id")
            ->where("t.id", $test_id)
            ->where("ct.course_id", $course_id)
            ->where("ct.module_id", $module_id)
            ->where("ct.is_active", 1)
            ->get()
            ->row_array();

        if (!$test) {
            $this->session->set_flashdata("message", ["danger", "Test not found."]);
            redirect(base_url($this->url . "/course/test_results/" . $course_id . "/" . $module_id . "/" . $test_id));
        }

        // Get all enrolled students in the course
        // Get students directly added to the course
        $direct_students = $this->db
            ->select('s.*, d.name as department_name')
            ->from("course_students cs")
            ->join("students s", "s.id = cs.student_id")
            ->join("departments d", "d.id = s.department", "left")
            ->where("cs.course_id", $course_id)
            ->where("cs.is_active", 1)
            ->where("s.is_active", 1)
            ->get()
            ->result_array();

        // Get students via groups
        $group_students = $this->db
            ->select('s.*, d.name as department_name')
            ->from("course_groups cg")
            ->join("groups g", "g.id = cg.group_id")
            ->join("group_members gm", "gm.group_id = g.id")
            ->join("students s", "s.id = gm.student_id")
            ->join("departments d", "d.id = s.department", "left")
            ->where("cg.course_id", $course_id)
            ->where("cg.is_active", 1)
            ->where("s.is_active", 1)
            ->get()
            ->result_array();

        // Get students via departments
        $dept_students = $this->db
            ->select('s.*, d.name as department_name')
            ->from("course_departments cd")
            ->join("departments d", "d.id = cd.department_id")
            ->join("students s", "s.department = d.id")
            ->where("cd.course_id", $course_id)
            ->where("s.is_active", 1)
            ->get()
            ->result_array();

        // Combine all students and remove duplicates
        $all_students = array_merge(
            $direct_students,
            $group_students,
            $dept_students
        );

        // Remove duplicates based on student ID
        $unique_students = [];
        $student_ids = [];
        foreach ($all_students as $student) {
            if (!in_array($student['id'], $student_ids)) {
                $student_ids[] = $student['id'];
                $unique_students[] = $student;
            }
        }

        // Get all questions for this test
        $questions = $this->common->get_test_questions($test_id);
        // Test-level flag from UI settings
        $sections_enabled = $this->common->is_sections_enabled_for_test($test_id);

        $section_context = $this->common->build_section_context($questions, $sections_enabled);
        $section_map = $section_context['map'];
        $questions_by_section = $section_context['grouped'];
        $section_order = $section_context['order'];

        // Preload all student solutions for this test once (to avoid per-student queries)
        $all_solutions = $this->db
            ->select('*')
            ->from('student_solutions')
            ->where('test_id', $test_id)
            ->where('course_id', $course_id)
            ->where('module_id', $module_id)
            ->order_by('submission_time', 'ASC')
            ->get()
            ->result_array();

        // Group solutions by student for fast lookup
        $solutions_by_student = [];
        foreach ($all_solutions as $solution) {
            $sid = $solution['student_id'];
            $qid = $solution['question_id'] ?? null;

            if (!isset($solutions_by_student[$sid])) {
                $solutions_by_student[$sid] = [
                    'by_time'     => [],
                    'by_question' => [],
                ];
            }

            $solutions_by_student[$sid]['by_time'][] = $solution;

            if ($qid !== null) {
                $solutions_by_student[$sid]['by_question'][$qid] = $solution;
            }
        }

        // Pre-calculate section scores per student using SQL aggregation (replaces nested loops)
        $section_scores_by_student = [];
        $section_totals_map = [];
        if ($sections_enabled && !empty($section_order)) {
            // Pre-calculate section totals from questions (sum of max_score per section - done once)
            foreach ($section_order as $section_id) {
                $section_questions = $questions_by_section[$section_id] ?? [];
                $section_total = 0;
                foreach ($section_questions as $question) {
                    $section_total += (float)($question['max_score'] ?? 0);
                }
                $section_totals_map[$section_id] = $section_total;
            }

            // Use SQL to calculate section_earned (sum of actual scores) per student per section
            $section_earned_query = $this->db
                ->select('ss.student_id, tq.section_id, SUM(COALESCE(ss.score, 0)) as section_earned')
                ->from('student_solutions ss')
                ->join('test_questions tq', 'tq.question_id = ss.question_id AND tq.test_id = ss.test_id', 'inner')
                ->where('ss.test_id', $test_id)
                ->where('ss.course_id', $course_id)
                ->where('ss.module_id', $module_id)
                ->group_by('ss.student_id, tq.section_id')
                ->get()
                ->result_array();

            // Build lookup map: $section_scores_by_student[student_id][section_id] = ['earned' => Y]
            // Note: section_total comes from $section_totals_map (calculated from questions above)
            foreach ($section_earned_query as $row) {
                $sid = $row['student_id'];
                $sec_id = (int)$row['section_id'];
                if (!isset($section_scores_by_student[$sid])) {
                    $section_scores_by_student[$sid] = [];
                }
                $section_scores_by_student[$sid][$sec_id] = [
                    'earned' => (float)$row['section_earned']
                ];
            }
        }

        // Get all submissions for this test
        $submissions = $this->db
            ->select("sts.*, s.name, s.email, s.phone_number, 
                     d.name as department_name, s.batch, s.registration_number")
            ->from("student_test_submission sts")
            ->join("students s", "s.id = sts.student_id")
            ->join("departments d", "d.id = s.department", "left")
            ->where("sts.course_id", $course_id)
            ->where("sts.module_id", $module_id)
            ->where("sts.test_id", $test_id)
            ->get()
            ->result_array();

        // Create a lookup array for submissions
        $submission_lookup = [];
        foreach ($submissions as $submission) {
            $submission_lookup[$submission['student_id']] = $submission;
        }

        // Prepare CSV data
        $filename = "test_results_{$test['title']}.csv";
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Add UTF-8 BOM to ensure proper character encoding
        fwrite($output, "\xEF\xBB\xBF");

        // Build headers
        $headers = [
            'Name', 'Email', 'Phone Number', 'Registration Number',
            'Batch', 'Department', 'Start IP', 'End IP', 'Browser',
            'Tab Switched', 'Start Time', 'Submit Time', 'Test Duration',
            'Test Status'
        ];

        // Add section score columns if sections are enabled
        if ($sections_enabled && !empty($section_order)) {
            foreach ($section_order as $section_id) {
                $section_name = isset($section_map[$section_id]) ? $section_map[$section_id] : 'Section';
                $headers[] = $section_name . ' Total Score';
                $headers[] = $section_name . ' Earned Score';
            }
        }

        // Add total score columns
        $headers[] = 'Total Score';
        $headers[] = 'Earned Score';
        $headers[] = 'Percentage';
        $headers[] = 'Result';
        $headers[] = 'Plagiarism Score';

        fputcsv($output, $headers);

        // Write data rows for all students
        foreach ($unique_students as $student) {
            $submission = isset($submission_lookup[$student['id']]) ? $submission_lookup[$student['id']] : null;
            
            // Initialize variables for IP and browser tracking
            $start_ip = '-';
            $end_ip = '-';
            $browser = '-';
            $start_time = '-';
            $end_time = '-';
            $duration_formatted = '-';
            $tab_changes = '-';
            $total_score = '-';
            $earned_score = '-';
            $percentage = 0;
            $result = '-';

            if ($submission) {
                // Get all solutions for this student's test submission from preloaded map (no DB query)
                $student_solution_group = $solutions_by_student[$student['id']] ?? ['by_time' => [], 'by_question' => []];
                $solutions_for_ip = $student_solution_group['by_time']; // Already ordered by submission_time

                // Process solutions to get IP and browser info
                if (!empty($solutions_for_ip)) {
                    foreach ($solutions_for_ip as $solution) {
                        if (!empty($solution['meta']) && $solution['meta'] !== '[]') {
                            $meta_data = json_decode($solution['meta'], true);
                            if (is_array($meta_data)) {
                                // Get browser info from first valid solution
                                if ($browser === '-' && isset($meta_data['userAgent'])) {
                                    $browser = $meta_data['userAgent'];
                                }
                                
                                // Get start IP from first solution
                                if ($start_ip === '-' && isset($meta_data['userIp'])) {
                                    $start_ip = $meta_data['userIp'];
                                }
                                
                                // Get end IP from last solution
                                if (isset($meta_data['userIp'])) {
                                    $end_ip = $meta_data['userIp'];
                                }
                            }
                        }
                    }
                }

                $start_time = isset($submission['created_at']) ? strtotime($submission['created_at']) : time();
                $end_time = isset($submission['submission_time']) ? strtotime($submission['submission_time']) : time();
                $duration = $end_time - $start_time;
                $duration_formatted = sprintf(
                    "%02d:%02d:%02d",
                    floor($duration / 3600),
                    floor(($duration % 3600) / 60),
                    $duration % 60
                );
                $tab_changes = $submission['tab_changes'] ?? 0;
                $total_score = $submission['total_score'] ?? 0;
                $earned_score = $submission['earned_score'] ?? 0;
                $percentage = $submission['percentage'] ?? 0;
                $result = ($percentage >= $test['pass_percentage']) ? 'Pass' : 'Fail';
            }
            $plagiarism_score = '-';
            if ($submission && !empty($submission['details'])) {
                $details = json_decode($submission['details'], true);
                if (!empty($details['problem_scores'])) {
                    foreach ($details['problem_scores'] as $problem) {
                        if (($problem['problem_type'] ?? '') === 'code' && isset($problem['plagiarism_score'])) {
                            $plagiarism_score = $problem['plagiarism_score'] . '%';
                            break; // just pick first coding problem
                        }
                    }
                }
            }

            // Get section scores from pre-calculated SQL aggregation (no nested loops)
            $section_scores = [];
            if ($sections_enabled && !empty($section_order)) {
                $student_id = $student['id'];
                $student_section_scores = $section_scores_by_student[$student_id] ?? [];

                foreach ($section_order as $section_id) {
                    $section_total = $section_totals_map[$section_id] ?? 0;
                    
                    if ($submission && isset($student_section_scores[$section_id])) {
                        // Use pre-calculated earned score from SQL, total from questions
                        $section_scores[] = $section_total;
                        $section_scores[] = $student_section_scores[$section_id]['earned'];
                    } elseif ($submission) {
                        // Student has submission but no solutions for this section (all 0s)
                        $section_scores[] = $section_total;
                        $section_scores[] = 0;
                    } else {
                        // Not attempted
                        $section_scores[] = '';
                        $section_scores[] = '';
                    }
                }
            }

            // Build row data
            $row = [
                $student['name'],
                $student['email'],
                $student['phone_number'],
                $student['registration_number'],
                $student['batch'] ?? '',
                $student['department_name'],
                $start_ip,
                $end_ip,
                $browser,
                $tab_changes,
                $start_time !== '-' ? date('d-m-Y H:i', $start_time) : '-',
                $end_time !== '-' ? date('d-m-Y H:i', $end_time) : '-',
                $duration_formatted,
                $submission ? 'Completed' : 'Not Attempted '
            ];

            // Add section scores if sections are enabled
            if ($sections_enabled && !empty($section_order)) {
                foreach ($section_scores as $score) {
                    $row[] = $score;
                }
            }

            // Add total score columns
            $row[] = $total_score;
            $row[] = $earned_score;
            $row[] = $percentage > 0 ? number_format($percentage, 2) . '%' : '0%';
            $row[] = $result;
            $row[] = $plagiarism_score;

            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    public function export_performance_report($course_id = null) {
        if (!$course_id) {
            redirect(base_url($this->url . "/course"));
        }

        $course = $this->db
            ->where('id', $course_id)
            ->get(TABLE_COURCES)
            ->row_array();
        if (!$course) {
            redirect(base_url($this->url . "/course"));
        }

        // Get all enrolled students in the course
        // Get students directly added to the course
        $direct_students = $this->db
            ->select('s.*, d.name as department_name')
            ->from("course_students cs")
            ->join("students s", "s.id = cs.student_id")
            ->join("departments d", "d.id = s.department", "left")
            ->where("cs.course_id", $course_id)
            ->where("cs.is_active", 1)
            ->where("s.is_active", 1)
            ->get()
            ->result_array();

        // Get students via groups
        $group_students = $this->db
            ->select('s.*, d.name as department_name')
            ->from("course_groups cg")
            ->join("groups g", "g.id = cg.group_id")
            ->join("group_members gm", "gm.group_id = g.id")
            ->join("students s", "s.id = gm.student_id")
            ->join("departments d", "d.id = s.department", "left")
            ->where("cg.course_id", $course_id)
            ->where("cg.is_active", 1)
            ->where("s.is_active", 1)
            ->get()
            ->result_array();

        // Get students via departments
        $dept_students = $this->db
            ->select('s.*, d.name as department_name')
            ->from("course_departments cd")
            ->join("departments d", "d.id = cd.department_id")
            ->join("students s", "s.department = d.id")
            ->where("cd.course_id", $course_id)
            ->where("s.is_active", 1)
            ->get()
            ->result_array();

        // Combine all students and remove duplicates
        $all_students = array_merge(
            $direct_students,
            $group_students,
            $dept_students
        );

        // Remove duplicates based on student ID
        $unique_students = [];
        $student_ids = [];
        foreach ($all_students as $student) {
            if (!in_array($student['id'], $student_ids)) {
                $student_ids[] = $student['id'];
                $unique_students[] = $student;
            }
        }

        $modules = $this->db
            ->select('cm.*')
            ->from('course_modules cm')
            ->where('cm.course_id', $course_id)
            ->where('cm.is_active', 1)
            ->get()
            ->result_array();

        $tests = $this->db
            ->select('t.*, ct.id as course_test_id, cm.id as module_id, cm.name as module_name')
            ->from('tests t')
            ->join('course_tests ct', 'ct.test_id = t.id')
            ->join('course_modules cm', 'cm.id = ct.module_id')
            ->where('ct.course_id', $course_id)
            ->get()
            ->result_array();

        $submissions = $this->db
            ->select('sts.*, s.name, s.registration_number, s.email, s.phone_number, d.name as department_name, s.batch, t.pass_percentage, t.title as test_title')
            ->from('student_test_submission sts')
            ->join('students s', 's.id = sts.student_id')
            ->join('departments d', 'd.id = s.department')
            ->join('tests t', 't.id = sts.test_id')
            ->where('sts.course_id', $course_id)
            ->get()
            ->result_array();

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="Course-Performance-Report_' . $course['course_code'] . '.csv"');
        $output = fopen('php://output', 'w');

        // Add UTF-8 BOM to ensure proper character encoding
        fwrite($output, "\xEF\xBB\xBF");

        // Prepare headers
        $headers = [
            'Name', 'Email', 'Phone No', 'Registration Number', 'Batch', 'Department'
        ];
        
        // Add module completion headers
        foreach ($modules as $module) {
            $headers[] = $module['name'] . ' - Completed Percentage';
        }

        // Add test performance headers
        foreach ($tests as $test) {
            $headers[] = $test['title'] . ' - Score';
            $headers[] = $test['title'] . ' - Percentage';
            $headers[] = $test['title'] . ' - Status';
        }
        
        fputcsv($output, $headers);

        // Calculate module-wise test counts
        $module_test_counts = [];
        foreach ($tests as $test) {
            if (!isset($module_test_counts[$test['module_id']])) {
                $module_test_counts[$test['module_id']] = 0;
            }
            $module_test_counts[$test['module_id']]++;
        }

        // Group submissions by student
        $student_stats = [];
        foreach ($submissions as $submission) {
            if (!isset($student_stats[$submission['student_id']])) {
                $student_stats[$submission['student_id']] = [
                    'name' => $submission['name'],
                    'email' => $submission['email'],
                    'phone_number' => $submission['phone_number'],
                    'registration_number' => $submission['registration_number'],
                    'department_name' => $submission['department_name'],
                    'batch' => $submission['batch'],
                    'module_completions' => array_fill_keys(array_column($modules, 'id'), 0),
                    'test_scores' => array_fill_keys(array_column($tests, 'id'), [
                        'score' => 0,
                        'percentage' => 0,
                        'status' => 'Not Attempted'
                    ])
                ];
            }
            
            // Count completed tests per module
            $student_stats[$submission['student_id']]['module_completions'][$submission['module_id']]++;
            
            // Store test scores
            $student_stats[$submission['student_id']]['test_scores'][$submission['test_id']] = [
                'score' => $submission['earned_score'] . '/' . $submission['total_score'],
                'percentage' => $submission['percentage'],
                'status' => ($submission['percentage'] >= $submission['pass_percentage']) ? 'Pass' : 'Fail'
            ];
        }

        // Output student data
        foreach ($unique_students as $student) {
            $stats = isset($student_stats[$student['id']]) ? $student_stats[$student['id']] : [
                'name' => $student['name'],
                'email' => $student['email'],
                'phone_number' => $student['phone_number'],
                'registration_number' => $student['registration_number'],
                'department_name' => $student['department_name'],
                'batch' => $student['batch'],
                'module_completions' => array_fill_keys(array_column($modules, 'id'), 0),
                'test_scores' => array_fill_keys(array_column($tests, 'id'), [
                    'score' => '0/0',
                    'percentage' => 0,
                    'status' => 'Not Attempted'
                ])
            ];

            $row = [
                $stats['name'],
                $stats['email'],
                $stats['phone_number'],
                $stats['registration_number'],
                $stats['batch'],
                $stats['department_name']
            ];

            // Calculate completion percentage for each module
            foreach ($modules as $module) {
                $completed_tests = $stats['module_completions'][$module['id']];
                $total_tests = $module_test_counts[$module['id']] ?? 0;
                $completion_percentage = $total_tests > 0 ? ($completed_tests / $total_tests) * 100 : 0;
                $row[] = number_format($completion_percentage, 2) . '%';
            }

            // Add test performance data
            foreach ($tests as $test) {
                $test_data = $stats['test_scores'][$test['id']];
                $row[] = $test_data['score'];
                $row[] = number_format($test_data['percentage'], 2) . '%';
                $row[] = $test_data['status'];
            }

            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    public function export_module_report($course_id = null, $module_id = null) {
        if (!$course_id || !$module_id) {
            redirect(base_url($this->url . "/course"));
        }

        $course = $this->db
            ->where('id', $course_id)
            ->get(TABLE_COURCES)
            ->row_array();
        if (!$course) {
            redirect(base_url($this->url . "/course"));
        }

        // Get module details
        $module = $this->db
            ->select('cm.*')
            ->from('course_modules cm')
            ->where('cm.course_id', $course_id)
            ->where('cm.id', $module_id)
            ->where('cm.is_active', 1)
            ->get()
            ->row_array();

        if (!$module) {
            $this->session->set_flashdata("message", ["danger", "Module not found."]);
            redirect(base_url($this->url . "/course/modules/" . $course_id));
        }

        // Get all enrolled students in the course
        // Get students directly added to the course
        $direct_students = $this->db
            ->select('s.*, d.name as department_name')
            ->from("course_students cs")
            ->join("students s", "s.id = cs.student_id")
            ->join("departments d", "d.id = s.department", "left")
            ->where("cs.course_id", $course_id)
            ->where("cs.is_active", 1)
            ->where("s.is_active", 1)
            ->get()
            ->result_array();

        // Get students via groups
        $group_students = $this->db
            ->select('s.*, d.name as department_name')
            ->from("course_groups cg")
            ->join("groups g", "g.id = cg.group_id")
            ->join("group_members gm", "gm.group_id = g.id")
            ->join("students s", "s.id = gm.student_id")
            ->join("departments d", "d.id = s.department", "left")
            ->where("cg.course_id", $course_id)
            ->where("cg.is_active", 1)
            ->where("s.is_active", 1)
            ->get()
            ->result_array();

        // Get students via departments
        $dept_students = $this->db
            ->select('s.*, d.name as department_name')
            ->from("course_departments cd")
            ->join("departments d", "d.id = cd.department_id")
            ->join("students s", "s.department = d.id")
            ->where("cd.course_id", $course_id)
            ->where("s.is_active", 1)
            ->get()
            ->result_array();

        // Combine all students and remove duplicates
        $all_students = array_merge(
            $direct_students,
            $group_students,
            $dept_students
        );

        // Remove duplicates based on student ID
        $unique_students = [];
        $student_ids = [];
        foreach ($all_students as $student) {
            if (!in_array($student['id'], $student_ids)) {
                $student_ids[] = $student['id'];
                $unique_students[] = $student;
            }
        }

        $tests = $this->db
            ->select('t.*, ct.id as course_test_id, cm.id as module_id, cm.name as module_name')
            ->from('tests t')
            ->join('course_tests ct', 'ct.test_id = t.id')
            ->join('course_modules cm', 'cm.id = ct.module_id')
            ->where('ct.course_id', $course_id)
            ->where('cm.id', $module_id)
            ->get()
            ->result_array();

        $submissions = $this->db
            ->select('sts.*, s.name, s.registration_number, s.email, s.phone_number, d.name as department_name, s.batch, t.pass_percentage, t.title as test_title')
            ->from('student_test_submission sts')
            ->join('students s', 's.id = sts.student_id')
            ->join('departments d', 'd.id = s.department')
            ->join('tests t', 't.id = sts.test_id')
            ->where('sts.course_id', $course_id)
            ->where('sts.module_id', $module_id)
            ->get()
            ->result_array();

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="Module-Performance-Report_' . $module['name'] . '.csv"');
        $output = fopen('php://output', 'w');

        // Add UTF-8 BOM to ensure proper character encoding
        fwrite($output, "\xEF\xBB\xBF");

        // Prepare headers
        $headers = [
            'Name', 'Email', 'Phone No', 'Registration Number', 'Batch', 'Department',
            'Module Completion', 'Total Tests', 'Tests Attempted', 'Tests Passed'
        ];
        
        // Add test performance headers
        foreach ($tests as $test) {
            $headers[] = $test['title'] . ' - Score';
            $headers[] = $test['title'] . ' - Percentage';
            $headers[] = $test['title'] . ' - Status';
            $headers[] = $test['title'] . ' - Attempt Date';
            $headers[] = $test['title'] . ' - Duration';
        }
        
        fputcsv($output, $headers);

        // Group submissions by student
        $student_stats = [];
        foreach ($submissions as $submission) {
            if (!isset($student_stats[$submission['student_id']])) {
                $student_stats[$submission['student_id']] = [
                    'name' => $submission['name'],
                    'email' => $submission['email'],
                    'phone_number' => $submission['phone_number'],
                    'registration_number' => $submission['registration_number'],
                    'department_name' => $submission['department_name'],
                    'batch' => $submission['batch'],
                    'tests_attempted' => 0,
                    'tests_passed' => 0,
                    'test_scores' => array_fill_keys(array_column($tests, 'id'), [
                        'score' => '0/0',
                        'percentage' => 0,
                        'status' => 'Not Attempted',
                        'attempt_date' => '-',
                        'duration' => '-'
                    ])
                ];
            }
            
            // Update test scores and stats
            $student_stats[$submission['student_id']]['tests_attempted']++;
            if ($submission['percentage'] >= $submission['pass_percentage']) {
                $student_stats[$submission['student_id']]['tests_passed']++;
            }
            
            // Calculate duration
            $start_time = strtotime($submission['created_at']);
            $end_time = strtotime($submission['submission_time']);
            $duration = $end_time - $start_time;
            $duration_formatted = sprintf(
                "%02d:%02d:%02d",
                floor($duration / 3600),
                floor(($duration % 3600) / 60),
                $duration % 60
            );
            
            $student_stats[$submission['student_id']]['test_scores'][$submission['test_id']] = [
                'score' => $submission['earned_score'] . '/' . $submission['total_score'],
                'percentage' => $submission['percentage'],
                'status' => ($submission['percentage'] >= $submission['pass_percentage']) ? 'Pass' : 'Fail',
                'attempt_date' => date('d/m/Y H:i:s', $start_time),
                'duration' => $duration_formatted
            ];
        }

        // Output student data
        foreach ($unique_students as $student) {
            $stats = isset($student_stats[$student['id']]) ? $student_stats[$student['id']] : [
                'name' => $student['name'],
                'email' => $student['email'],
                'phone_number' => $student['phone_number'],
                'registration_number' => $student['registration_number'],
                'department_name' => $student['department_name'],
                'batch' => $student['batch'],
                'tests_attempted' => 0,
                'tests_passed' => 0,
                'test_scores' => array_fill_keys(array_column($tests, 'id'), [
                    'score' => '0/0',
                    'percentage' => 0,
                    'status' => 'Not Attempted',
                    'attempt_date' => '-',
                    'duration' => '-'
                ])
            ];

            $total_tests = count($tests);
            $module_completion = $total_tests > 0 ? 
                number_format(($stats['tests_attempted'] / $total_tests) * 100, 2) : 0;

            $row = [
                $stats['name'],
                $stats['email'],
                $stats['phone_number'],
                $stats['registration_number'],
                $stats['batch'],
                $stats['department_name'],
                $module_completion . '%',
                $total_tests,
                $stats['tests_attempted'],
                $stats['tests_passed']
            ];

            // Add test performance data
            foreach ($tests as $test) {
                $test_data = $stats['test_scores'][$test['id']];
                $row[] = $test_data['score'];
                $row[] = number_format($test_data['percentage'], 2) . '%';
                $row[] = $test_data['status'];
                $row[] = $test_data['attempt_date'];
                $row[] = $test_data['duration'];
            }

            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }




    public function export_detailed_test_results($course_id = null, $module_id = null, $test_id = null) {
        if (!$course_id || !$module_id || !$test_id) {
            redirect(base_url($this->url . "/course"));
        }

        // Get test details
        $test = $this->db
            ->select("t.*, ct.start_date, ct.end_date")
            ->from("tests t")
            ->join("course_tests ct", "ct.test_id = t.id")
            ->where("t.id", $test_id)
            ->where("ct.course_id", $course_id)
            ->where("ct.module_id", $module_id)
            ->where("ct.is_active", 1)
            ->get()
            ->row_array();

        if (!$test) {
            $this->session->set_flashdata("message", ["danger", "Test not found."]);
            redirect(base_url($this->url . "/course/test_results/" . $course_id . "/" . $module_id . "/" . $test_id));
        }

        // Get all questions for this test
        $questions = $this->common->get_test_questions($test_id);
        // Test-level flag from UI settings
        $sections_enabled = $this->common->is_sections_enabled_for_test($test_id);

        $section_context = $this->common->build_section_context($questions, $sections_enabled);
        $section_map = $section_context['map'];
        $questions_by_section = $section_context['grouped'];
        $section_order = $section_context['order'];

        // Pre-calculate section scores per student using SQL aggregation (replaces nested loops)
        $section_scores_by_student = [];
        $section_totals_map = [];
        if ($sections_enabled && !empty($section_order)) {
            // Pre-calculate section totals from questions (sum of max_score per section - done once)
            foreach ($section_order as $section_id) {
                $section_questions = $questions_by_section[$section_id] ?? [];
                $section_total = 0;
                foreach ($section_questions as $question) {
                    $section_total += (float)($question['max_score'] ?? 0);
                }
                $section_totals_map[$section_id] = $section_total;
            }

            // Use SQL to calculate section_earned (sum of actual scores) per student per section
            $section_earned_query = $this->db
                ->select('ss.student_id, tq.section_id, SUM(COALESCE(ss.score, 0)) as section_earned')
                ->from('student_solutions ss')
                ->join('test_questions tq', 'tq.question_id = ss.question_id AND tq.test_id = ss.test_id', 'inner')
                ->where('ss.test_id', $test_id)
                ->where('ss.course_id', $course_id)
                ->where('ss.module_id', $module_id)
                ->group_by('ss.student_id, tq.section_id')
                ->get()
                ->result_array();

            // Build lookup map: $section_scores_by_student[student_id][section_id] = ['earned' => Y]
            // Note: section_total comes from $section_totals_map (calculated from questions above)
            foreach ($section_earned_query as $row) {
                $sid = $row['student_id'];
                $sec_id = (int)$row['section_id'];
                if (!isset($section_scores_by_student[$sid])) {
                    $section_scores_by_student[$sid] = [];
                }
                $section_scores_by_student[$sid][$sec_id] = [
                    'earned' => (float)$row['section_earned']
                ];
            }
        }

        $ordered_questions = [];
        if ($sections_enabled && !empty($questions_by_section)) {
            $ordered_sections = !empty($section_order) ? $section_order : array_keys($questions_by_section);
            foreach ($ordered_sections as $section_key) {
                if (!isset($questions_by_section[$section_key])) {
                    continue;
                }
                $section_name = isset($section_map[$section_key])
                    ? $section_map[$section_key]
                    : (is_numeric($section_key) ? 'Section ' . $section_key : 'Unassigned Questions');

                foreach ($questions_by_section[$section_key] as $question) {
                    $ordered_questions[] = [
                        'section_key' => $section_key,
                        'section_name' => $section_name,
                        'question' => $question
                    ];
                }
            }
        } else {
            foreach ($questions as $question) {
                $ordered_questions[] = [
                    'section_key' => null,
                    'section_name' => null,
                    'question' => $question
                ];
            }
        }

        // Preload all student solutions for this test once (to avoid per-student queries)
        $all_solutions = $this->db
            ->select('*')
            ->from('student_solutions')
            ->where('test_id', $test_id)
            ->where('course_id', $course_id)
            ->where('module_id', $module_id)
            ->order_by('submission_time', 'ASC')
            ->get()
            ->result_array();

        // Group solutions by student for fast lookup
        $solutions_by_student = [];
        foreach ($all_solutions as $solution) {
            $sid = $solution['student_id'];
            $qid = $solution['question_id'] ?? null;

            if (!isset($solutions_by_student[$sid])) {
                $solutions_by_student[$sid] = [
                    'by_time'     => [],
                    'by_question' => [],
                ];
            }

            $solutions_by_student[$sid]['by_time'][] = $solution;

            if ($qid !== null) {
                $solutions_by_student[$sid]['by_question'][$qid] = $solution;
            }
        }

        // Get all enrolled students in the course
        // Get students directly added to the course
        $direct_students = $this->db
            ->select('s.*, d.name as department_name')
            ->from("course_students cs")
            ->join("students s", "s.id = cs.student_id")
            ->join("departments d", "d.id = s.department", "left")
            ->where("cs.course_id", $course_id)
            ->where("cs.is_active", 1)
            ->where("s.is_active", 1)
            ->get()
            ->result_array();

        // Get students via groups
        $group_students = $this->db
            ->select('s.*, d.name as department_name')
            ->from("course_groups cg")
            ->join("groups g", "g.id = cg.group_id")
            ->join("group_members gm", "gm.group_id = g.id")
            ->join("students s", "s.id = gm.student_id")
            ->join("departments d", "d.id = s.department", "left")
            ->where("cg.course_id", $course_id)
            ->where("cg.is_active", 1)
            ->where("s.is_active", 1)
            ->get()
            ->result_array();

        // Get students via departments
        $dept_students = $this->db
            ->select('s.*, d.name as department_name')
            ->from("course_departments cd")
            ->join("departments d", "d.id = cd.department_id")
            ->join("students s", "s.department = d.id")
            ->where("cd.course_id", $course_id)
            ->where("s.is_active", 1)
            ->get()
            ->result_array();

        // Combine all students and remove duplicates
        $all_students = array_merge(
            $direct_students,
            $group_students,
            $dept_students
        );

        // Remove duplicates based on student ID
        $unique_students = [];
        $student_ids = [];
        foreach ($all_students as $student) {
            if (!in_array($student['id'], $student_ids)) {
                $student_ids[] = $student['id'];
                $unique_students[] = $student;
            }
        }

        // Get all submissions for this test
        $submissions = $this->db
            ->select("sts.*, s.name, s.email, s.phone_number, 
                     d.name as department_name, s.batch, s.registration_number")
            ->from("student_test_submission sts")
            ->join("students s", "s.id = sts.student_id")
            ->join("departments d", "d.id = s.department", "left")
            ->where("sts.course_id", $course_id)
            ->where("sts.module_id", $module_id)
            ->where("sts.test_id", $test_id)
            ->get()
            ->result_array();
        // print_r($submissions); exit;
        // Create a lookup array for submissions
        $submission_lookup = [];
        foreach ($submissions as $submission) {
            $submission_lookup[$submission['student_id']] = $submission;
        }

        // Prepare CSV data
        $filename = "detailed_test_results_{$test['title']}.csv";
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Add UTF-8 BOM to ensure proper character encoding
        fwrite($output, "\xEF\xBB\xBF");

        // Write headers
        $headers = [
            'Name', 'Email', 'Phone Number', 'Registration Number',
            'Batch', 'Department', 'Start IP', 'End IP', 'Browser',
            'Tab Switched', 'Start Time', 'Submit Time', 'Test Duration',
            'Test Status'
        ];

        // Add section score columns if sections are enabled
        if ($sections_enabled && !empty($section_order)) {
            foreach ($section_order as $section_id) {
                $section_name = isset($section_map[$section_id]) ? $section_map[$section_id] : 'Section';
                $headers[] = $section_name . ' Total Score';
                $headers[] = $section_name . ' Earned Score';
            }
        }

        // Add total score columns
        $headers[] = 'Total Score';
        $headers[] = 'Earned Score';
        $headers[] = 'Percentage';
        $headers[] = 'Result';
        $headers[] = 'Plagiarism Score';

        // Add question headers
        foreach ($ordered_questions as $entry) {
            $question = $entry['question'];
            $headers[] = ($question['question_title'] ?? '') . ' - Submission';
            $headers[] = ($question['question_title'] ?? '') . ' - Result';
        }

        fputcsv($output, $headers);

        // Write data rows for all students
        foreach ($unique_students as $student) {
            $submission = isset($submission_lookup[$student['id']]) ? $submission_lookup[$student['id']] : null;
            
            // Initialize variables for IP and browser tracking
            $start_ip = '-';
            $end_ip = '-';
            $browser = '-';
            $start_time = '-';
            $end_time = '-';
            $duration_formatted = '-';
            $tab_changes = '-';
            $total_score = '-';
            $earned_score = '-';
            $percentage = 0;
            $result = '-';

            if ($submission) {
                // Get all solutions for this student's test submission from preloaded map
                $student_solution_group = $solutions_by_student[$student['id']] ?? ['by_time' => [], 'by_question' => []];
                $solutions_for_ip = $student_solution_group['by_time'];

                // Process solutions to get IP and browser info
                if (!empty($solutions_for_ip)) {
                    foreach ($solutions_for_ip as $solution) {
                        if (!empty($solution['meta']) && $solution['meta'] !== '[]') {
                            $meta_data = json_decode($solution['meta'], true);
                            if (is_array($meta_data)) {
                                // Get browser info from first valid solution
                                if ($browser === '-' && isset($meta_data['userAgent'])) {
                                    $browser = $meta_data['userAgent'];
                                }
                                
                                // Get start IP from first solution
                                if ($start_ip === '-' && isset($meta_data['userIp'])) {
                                    $start_ip = $meta_data['userIp'];
                                }
                                
                                // Get end IP from last solution
                                if (isset($meta_data['userIp'])) {
                                    $end_ip = $meta_data['userIp'];
                                }
                            }
                        }
                    }
                }

                $start_time = isset($submission['created_at']) ? strtotime($submission['created_at']) : time();
                $end_time = isset($submission['submission_time']) ? strtotime($submission['submission_time']) : time();
                $duration = $end_time - $start_time;
                $duration_formatted = sprintf(
                    "%02d:%02d:%02d",
                    floor($duration / 3600),
                    floor(($duration % 3600) / 60),
                    $duration % 60
                );
                $tab_changes = $submission['tab_changes'] ?? 0;
                $total_score = $submission['total_score'] ?? 0;
                $earned_score = $submission['earned_score'] ?? 0;
                $percentage = $submission['percentage'] ?? 0;
                $result = ($percentage >= $test['pass_percentage']) ? 'Pass' : 'Fail';
            }
            $plagiarism_score = '-';
            if ($submission && !empty($submission['details'])) {
                $details = json_decode($submission['details'], true);
                if (!empty($details['problem_scores'])) {
                    foreach ($details['problem_scores'] as $problem) {
                        if (($problem['problem_type'] ?? '') === 'code' && isset($problem['plagiarism_score'])) {
                            $plagiarism_score = $problem['plagiarism_score'] . '%';
                            break; // pick the first code problem's score
                        }
                    }
                }
            }

            // Get section scores from pre-calculated SQL aggregation (no nested loops)
            $section_scores = [];
            if ($sections_enabled && !empty($section_order)) {
                $student_id = $student['id'];
                $student_section_scores = $section_scores_by_student[$student_id] ?? [];

                foreach ($section_order as $section_id) {
                    $section_total = $section_totals_map[$section_id] ?? 0;
                    
                    if ($submission && isset($student_section_scores[$section_id])) {
                        // Use pre-calculated earned score from SQL, total from questions
                        $section_scores[] = $section_total;
                        $section_scores[] = $student_section_scores[$section_id]['earned'];
                    } elseif ($submission) {
                        // Student has submission but no solutions for this section (all 0s)
                        $section_scores[] = $section_total;
                        $section_scores[] = 0;
                    } else {
                        // Not attempted
                        $section_scores[] = '';
                        $section_scores[] = '';
                    }
                }
            }

            $row = [
                $student['name'],
                $student['email'],
                $student['phone_number'],
                $student['registration_number'],
                $student['batch'] ?? '',
                $student['department_name'],
                $start_ip,
                $end_ip,
                $browser,
                $tab_changes,
                $start_time !== '-' ? date('d-m-Y H:i', $start_time) : '-',
                $end_time !== '-' ? date('d-m-Y H:i', $end_time) : '-',
                $duration_formatted,
                $submission ? 'Completed' : 'Not Attempted '
            ];

            // Add section scores if sections are enabled
            if ($sections_enabled && !empty($section_order)) {
                foreach ($section_scores as $score) {
                    $row[] = $score;
                }
            }

            // Add total score columns
            $row[] = $total_score;
            $row[] = $earned_score;
            $row[] = $percentage > 0 ? number_format($percentage, 2) . '%' : '0%';
            $row[] = $result;
            $row[] = $plagiarism_score;

            // Get solutions for this submission from preloaded map (no DB query)
            $student_solution_group = $solutions_by_student[$student['id']] ?? ['by_time' => [], 'by_question' => []];
            $solution_map = $student_solution_group['by_question']; // Already indexed by question_id

            // Add question results
            foreach ($ordered_questions as $entry) {
                $question = $entry['question'];
                $solution = isset($solution_map[$question['question_id']]) ? $solution_map[$question['question_id']] : null;

                // Debug: print solution data
                // print_r($solution); exit;

                if ($solution) {
                    // Normalize submission (answer) and force Excel to treat it as text
                    $submission_value = $solution['answer'] ?? '-';
                    if ($submission_value !== '-' && $submission_value !== '') {
                        if (is_string($submission_value)) {
                            $decoded = json_decode($submission_value, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $submission_value = json_encode($decoded);
                            }
                        }
                        $submission_value = "\t" . (string)$submission_value;
                    }
                    $row[] = $submission_value;
                    // Result string prefixed with a tab to avoid Excel date auto-formatting
                    $result_text = "\t" . (($solution['score'] ?? 0) . '/' . ($question['max_score'] ?? 0));
                    $row[] = $result_text;
                } else {
                    $row[] = '-';
                    // If the test is completed for this student, show 0/max, else keep '-'
                    if ($submission && (isset($submission['finished']) ? intval($submission['finished']) === 1 : true)) {
                        $not_attempted_result = "\t0/" . ($question['max_score'] ?? 0);
                        $row[] = $not_attempted_result;
                    } else {
                        $row[] = '-';
                    }
                }
            }

            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    public function sync_test_results($course_id = null, $module_id = null, $test_id = null) {
        // Validate input parameters
        if (!$course_id || !$module_id || !$test_id) {
            $response = array(
                'status' => 'error',
                'message' => 'Missing required parameters',
                'data' => null
            );
            echo json_encode($response);
            return;
        }

        // Return immediately with success response
        $response = array(
            'status' => 'success',
            'message' => 'Sync process started',
            'data' => null
        );
        echo json_encode($response);

        // Start background process
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        // Get test details
        // $test = $this->db_model->get_row('tests', [
        //     'id' => $test_id,
        //     'college_id' => $this->college['id']
        // ], 'id, title, challenge_id');

        
        $test = $this->common->get_test_details($test_id,$this->college['id']);

        if (!$test) {
            $this->db->insert('sync_logs', [
                'type' => 'test_sync'.$course_id.'_'.$module_id.'_'.$test_id,
                'status' => 'error',
                'message' => 'Test not found',
                'data' => json_encode(['test_id' => $test_id]),
                'created_at' => date('Y-m-d H:i:s')
            ]);
            return;
        }

        // Get challenge stats from OneCompiler
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://onecompiler.com/api/v1/challenges/stats?access_token=' . ONE_COMPILER_API_KEY . '&challengeIds=' . $test['challenge_id'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        
        $stats_response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $this->db->insert('sync_logs', [
                'type' => 'test_sync'.$course_id.'_'.$module_id.'_'.$test_id,
                'status' => 'error',
                'message' => 'Failed to fetch challenge stats',
                'data' => json_encode(['error' => $err]),
                'created_at' => date('Y-m-d H:i:s')
            ]);
            return;
        }

        $stats_data = json_decode($stats_response, true);
        
        if (!isset($stats_data['status']) || $stats_data['status'] !== 'success' || 
            !isset($stats_data['stats'][$test['challenge_id']])) {
            $this->db->insert('sync_logs', [
                'type' => 'test_sync'.$course_id.'_'.$module_id.'_'.$test_id,
                'status' => 'error',
                'message' => 'Invalid response from challenge stats API',
                'data' => json_encode($stats_data),
                'created_at' => date('Y-m-d H:i:s')
            ]);
            return;
        }

        $challenge_submissions = $stats_data['stats'][$test['challenge_id']];
        
        if (empty($challenge_submissions)) {
            $this->db->insert('sync_logs', [
                'type' => 'test_sync'.$course_id.'_'.$module_id.'_'.$test_id,
                'status' => 'success',
                'message' => 'No submissions found to sync',
                'data' => null,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            return;
        }

        // Filter submissions to only include in-progress ones
        $in_progress_submissions = array_filter($challenge_submissions, function($submission) {
            return !isset($submission['finished']) || $submission['finished'] === false;
        });

        if (empty($in_progress_submissions)) {
            $this->db->insert('sync_logs', [
                'type' => 'test_sync'.$course_id.'_'.$module_id.'_'.$test_id,
                'status' => 'success',
                'message' => 'No in-progress submissions to sync',
                'data' => null,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            return;
        }

        // Get external IDs from in-progress submissions
        $external_ids = array_map(function($submission) {
            return $submission['user']['_id'];
        }, $in_progress_submissions);

        if (empty($external_ids)) {
            $this->db->insert('sync_logs', [
                'type' => 'test_sync'.$course_id.'_'.$module_id.'_'.$test_id,
                'status' => 'success',
                'message' => 'No valid external IDs found',
                'data' => null,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            return;
        }

        // Get students by external IDs who haven't submitted yet
        $students = $this->db->query("
            SELECT DISTINCT s.* 
            FROM students s
            LEFT JOIN student_test_submission sts ON s.id = sts.student_id AND sts.test_id = ?
            WHERE (sts.id IS NULL OR sts.finished = 0)
            AND s.college_id = ?
            AND s.external_id IN ?
        ", [$test_id, $this->college['id'], $external_ids])->result_array();

        if (empty($students)) {
            $this->db->insert('sync_logs', [
                'type' => 'test_sync'.$course_id.'_'.$module_id.'_'.$test_id,
                'status' => 'success',
                'message' => 'No pending submissions to sync',
                'data' => null,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            return;
        }

        $processed_count = 0;
        $error_count = 0;
     
        
        // Process each student's submission
        foreach ($students as $student) {
            try {
                // Find student's submission in challenge stats
                $student_submission = null;
                foreach ($challenge_submissions as $submission) {
                    if ($submission['user']['_id'] === $student['external_id']) {
                        $student_submission = $submission;
                        break;
                    }
                }

                // Skip if no submission found or already finished
                if (!$student_submission || isset($student_submission['finished']) && $student_submission['finished']) {
                    continue;
                }

                // Make API call to get detailed submission
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://onecompiler.com/api/v1/challenges/submission/' . $test['challenge_id'] . '/' . $student['external_id'] . '?access_token=' . ONE_COMPILER_API_KEY,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json'
                    ),
                ));
                
                $api_response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);

                if ($err) {
                    $error_count++;
                    continue;
                }

                $submission_data = json_decode($api_response, true);
                
                if (!isset($submission_data['status']) || $submission_data['status'] !== 'success') {
                    $error_count++;
                    continue;
                }

                if (isset($student['submission_id']) && $student['submission_id'] !== null) {
                    $this->db->delete('student_solutions', [
                        'student_id' => $student['id'],
                        'test_id' => $test_id,
                        'course_id' => $course_id,
                        'module_id' => $module_id
                    ]);
                }
                // Calculate total score using common method
                $total_score = $this->common->get_test_total_score($test_id);

                // Process submission data for earned score
                $earned_score = 0;
                $problem_scores = [];

                $problem_type_mapper = [
                    'multipleChoice' => 1,
                    'code' => 2,
                    'fillInTheBlank' => 3
                ];

                if (isset($submission_data['problemProgress']) && is_array($submission_data['problemProgress'])) {
                    foreach ($submission_data['problemProgress'] as $problem) {
                        $problem_details = null;
                        foreach ($submission_data['problems'] as $p) {
                            if ($p['_id'] === $problem['problem']['_id']) {
                                $problem_details = $p;
                                break;
                            }
                        }

                        if ($problem_details) {
                            $earned_problem_score = isset($problem['score']) ? floatval($problem['score']) : 0;
                            $earned_score += $earned_problem_score;

                            $problem_type = isset($problem_details['properties']['problemType']) ?
                                $problem_details['properties']['problemType'] : '';

                            $question = $this->db_model->get_row(
                                'question_bank',
                                ['question_title' => $problem_details['title']],
                                'id, score'
                            );

                            $problem_score = $question['score'] ?? 0;

                            $problem_scores[] = [
                                'problem_id' => $problem['problem']['_id'],
                                'title' => $problem_details['title'],
                                'max_score' => $problem_score,
                                'earned_score' => $earned_problem_score,
                                'submissions' => isset($problem['metrics']['submissions']) ?
                                    intval($problem['metrics']['submissions']) : 0,
                                'time_spent' => isset($problem['metrics']['timeSpent']) ?
                                    intval($problem['metrics']['timeSpent']) : 0,
                                'problem_type' => $problem_type,
                                'testcase_result' => isset($problem['testcaseLevelResult'])?$problem['testcaseLevelResult'] :'',
                                'plagiarism_score' => isset($problem['plagiarismScore']) ? $problem['plagiarismScore'] : '' // plagiarismScore  added
                            ];

                            if ($question) {
                                $meta = isset($problem['userEnvData']) ? $problem['userEnvData'] : [];

                                $solution_data = [
                                    'student_id' => $student['id'],
                                    'test_id' => $test_id,
                                    'course_id' => $course_id,
                                    'module_id' => $module_id,
                                    'question_id' => $question['id'],
                                    'problem_id' => $problem['problem']['_id'],
                                    'score' => $earned_problem_score,
                                    'max_score' => $problem_score,
                                    'submission_time' => date('Y-m-d H:i:s'),
                                    'time_spent' => isset($problem['metrics']['timeSpent']) ? 
                                        intval($problem['metrics']['timeSpent']) : 0,
                                    'problem_type' => isset($problem_type_mapper[$problem_type]) ? $problem_type_mapper[$problem_type] : 0,
                                    'meta' => json_encode($meta)
                                ];
                                
                                switch ($problem_type) {
                                    case 'code':
                                        if (isset($problem['solution']['code'])) {
                                            $code_solution = $problem['solution']['code'];
                                            $solution_data['language'] = isset($code_solution['properties']['language']) ? 
                                                $code_solution['properties']['language'] : '';
                                            $testcase_result = isset($problem['testcaseLevelResult']) ? $problem['testcaseLevelResult'] : '';
                                            $plagiarism_score = isset($problem['plagiarismScore']) ? $problem['plagiarismScore'] :''; // plagiarismScore  added
                                            $solution_data['solution'] = json_encode(['files' => $code_solution['properties']['files'],'testcase_result' => $testcase_result,'plagiarism_score'=> $plagiarism_score]);
                                        }
                                        break;
                                        
                                    case 'fillInTheBlank':
                                        if (isset($problem['solution']['fillInTheBlank'])) {
                                            $solution_data['solution'] = json_encode($problem['solution']['fillInTheBlank']);
                                        }
                                        break;
                                        
                                    case 'multipleChoice':
                                        if (isset($problem['solution']['multipleChoice'])) {
                                            $mcq_options = isset($problem_details['properties']['options']['multipleChoice']) ? 
                                                $problem_details['properties']['options']['multipleChoice'] : null;
                                            
                                            if ($mcq_options) {
                                                $solution_data['solution'] = json_encode([
                                                    'options' => $mcq_options['options'],
                                                    'correct_answer' => $mcq_options['answer'],
                                                    'answer' => $problem['solution']['multipleChoice']['answer']
                                                ]);
                                            } else {
                                                $solution_data['solution'] = json_encode($problem['solution']['multipleChoice']);
                                            }
                                        }
                                        break;
                                }
                                
                                $this->db->insert('student_solutions', $solution_data);
                            }
                        }
                    }
                }
                // // Check if submission already exists for this student and test
                $existing_submission = $this->db->get_where('student_test_submission', [
                    'student_id' => $student['id'],
                    'test_id'    => $test_id,
                    'course_id' => $course_id,
                    'module_id' => $module_id
                ])->row_array();

                // print_r($existing_submission);
                // exit;

                // Check if submission already exists for this student and test
                $existing_submission_id = $existing_submission ? $existing_submission['id'] : null;
                // print_r($existing_submission_id);
                // exit;
                if ($existing_submission_id) {
                    // Update existing submission record
                $this->db->where('id', $existing_submission_id);
                $this->db->update('student_test_submission', [
                    'challenge_id' => $test['challenge_id'],
                    'challenge_user_id' => $student['external_id'],
                    'total_score' => floatval($total_score),
                    'earned_score' => floatval($earned_score),
                    'percentage' => $total_score > 0 ? ($earned_score / $total_score) * 100 : 0,
                    'submission_time' => $submission_data['challenegeProgress']['updated'],
                    'tab_changes' => isset($submission_data['challenegeProgress']['tabChanges']) ? 
                        intval($submission_data['challenegeProgress']['tabChanges']) : 0,
                    'finished' => 1,
                    'sync_count' => 1 + intval($existing_submission['sync_count']),
                    'details' => json_encode([
                        'problem_scores' => $problem_scores,
                        'sessionRecordings' => $submission_data['challenegeProgress']['sessionRecordings'] ?? [],
                        'trackingData' => $submission_data['challenegeProgress']['trackingData'] ?? []
                    ])
                ]);

                } 
                else {
                // Create test submission record
                $submission = [
                    'student_id' => $student['id'],
                    'test_id' => $test_id,
                    'course_id' => $course_id,
                    'module_id' => $module_id,
                    'challenge_id' => $test['challenge_id'],
                    'challenge_user_id' => $student['external_id'],
                    'total_score' => floatval($total_score),
                    'earned_score' => floatval($earned_score),
                    'percentage' => $total_score > 0 ? ($earned_score / $total_score) * 100 : 0,
                    'submission_time' => $submission_data['challenegeProgress']['updated'],
                    'tab_changes' => isset($submission_data['challenegeProgress']['tabChanges']) ? 
                        intval($submission_data['challenegeProgress']['tabChanges']) : 0,
                    'finished' => 1,
                    'details' => json_encode([
                    'problem_scores' => $problem_scores,
                    'sessionRecordings' => $submission_data['challenegeProgress']['sessionRecordings'] ?? [],
                    'trackingData' => $submission_data['challenegeProgress']['trackingData'] ?? []
                    ]),
                    'created_at' => $submission_data['challenegeProgress']['created'],
                    'sync_count' => 1 // sync count starts at 1
                ];

                $this->db->insert('student_test_submission', $submission);
                }
                $processed_count++;
            } catch (Exception $e) {
                $error_count++;
            }
        }

        // Log final status
        $this->db->insert('sync_logs', [
            'type' => 'test_sync'.$course_id.'_'.$module_id.'_'.$test_id,
            'status' => 'success',
            'message' => 'Sync completed',
            'data' => json_encode([
                'processed' => $processed_count,
                'errors' => $error_count
            ]),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function get_sync_status($course_id = null, $module_id = null, $test_id = null) {
        // Validate input parameters
        if (!$course_id || !$module_id || !$test_id) {
            $response = array(
                'status' => 'error',
                'message' => 'Missing required parameters',
                'data' => null
            );
            echo json_encode($response);
            return;
        }

        // Get the latest sync log for this test
        // $is_first_sync = false;
        $sync_type = 'test_sync' . $course_id . '_' . $module_id . '_' . $test_id;
        $sync_log = $this->db->query("
            SELECT * FROM sync_logs 
            WHERE type = ?
            ORDER BY created_at DESC 
            LIMIT 1
        ", [$sync_type])->row_array();

        if (!$sync_log) {
            // $is_first_sync = true;
            $response = array(
                'status' => 'pending',
                'message' => 'No sync status found',
                'data' => null,
                // '$is_first_sync'=>$is_first_sync
            );
            echo json_encode($response);
            return;
        }

        // Check if sync is completed or failed
        if ($sync_log['status'] === 'success') {
            if ($sync_log['message'] === 'Sync completed') {
                $response = array(
                    'status' => 'completed',
                    'message' => 'Sync completed successfully',
                    'data' => json_decode($sync_log['data'], true)
                );
            } else if (in_array($sync_log['message'], [
                'No submissions found to sync',
                'No in-progress submissions to sync',
                'No valid external IDs found',
                'No pending submissions to sync'
            ])) {
                $response = array(
                    'status' => 'completed',
                    'message' => $sync_log['message'],
                    'data' => null
                );
            } else {
                $response = array(
                    'status' => 'in_progress',
                    'message' => 'Sync in progress',
                    'data' => null
                );
            }
        } else if ($sync_log['status'] === 'error') {
            $response = array(
                'status' => 'error',
                'message' => $sync_log['message'],
                'data' => json_decode($sync_log['data'], true)
            );
        } else {
            $response = array(
                'status' => 'in_progress',
                'message' => 'Sync in progress',
                'data' => null
            );
        }

        echo json_encode($response);
        return;
    }
}





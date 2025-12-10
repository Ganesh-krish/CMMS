<?php

use function PHPSTORM_META\type;

defined('BASEPATH') or exit('No direct script access allowed');

class db_model extends CI_Model
{

    public function __construct()
    {
    }

    /**
     * Fetch all rows from a table.
     * 
     * @param string $table
     * @param array $conditions
     * @param string $order_by
     * @param string $order_direction
     * @return array
     */
    public function get_all($table, $conditions = [],$select="*", $order_by = '', $order_direction = 'ASC') {
        $this->db->select($select);
        $this->db->from($table);
        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                if (is_array($value)) {
                    // Use WHERE IN if the value is an array
                    $this->db->where_in($key, $value);
                } else {
                    // Otherwise, apply the regular WHERE condition
                    $this->db->where($key, $value);
                }
            }
        }
        if ($order_by) {
            $this->db->order_by($order_by, $order_direction);
        }
        $query = $this->db->get();
        if ($query === false) {
            return [];
        }
        return $query->result_array();
    }

    public function get_distinct($table, $column, $conditions = [])
    {
        $this->db->distinct();
        $this->db->select($column);
        $this->db->from($table);
    
        if (!empty($conditions)) {
            $this->db->where($conditions);
        }
    
        $query = $this->db->get();
        $result = $query->result_array();
    
        // Extract the column values from the result
        $values = array_column($result, $column);
        return $values;
    }


    /**
     * Fetch a single row from a table.
     * 
     * @param string $table
     * @param array $conditions
     * @return array|null
     */
    public function get_row($table,$conditions = [],$select="*") {
        $this->db->select($select);
        $this->db->from($table);
        $this->db->where($conditions);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Insert a record into a table.
     * 
     * @param string $table
     * @param array $data
     * @return int|bool
     */
    public function insert($table, $data) {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }
    /**
     * Insert a record into a table.
     * 
     * @param string $table
     * @param array $data
     * @return int|bool
     */
    public function bulk_insert($table, $data) {
        $this->db->insert_batch($table, $data);
        if ($this->db->affected_rows() > 0) {
            return true; 
        } else {
            return false;  
        } 
    }

    /**
     * Update records in a table.
     * 
     * @param string $table
     * @param array $data
     * @param array $conditions
     * @return bool
     */
    public function update($table, $data, $conditions = []) {
        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                if (is_array($value)) {
                    // Use WHERE IN if the value is an array
                    $this->db->where_in($key, $value);
                } else {
                    // Otherwise, apply the regular WHERE condition
                    $this->db->where($key, $value);
                }
            }
        } 
        return $this->db->update($table, $data);
    }

    /**
     * Delete records from a table.
     * 
     * @param string $table
     * @param array $conditions
     * @return bool
     */
    public function delete($table, $conditions = []) {
        $this->db->where($conditions);
        return $this->db->delete($table);
    }

    /**
     * Count rows in a table with conditions.
     * 
     * @param string $table
     * @param array $conditions
     * @return int
     */
    public function count($table, $conditions = []) {
        $this->db->where($conditions);
        return $this->db->count_all_results($table);
    }

    /**
     * Custom query execution.
     * 
     * @param string $query
     * @param array $bindings
     * @return array
     */
    public function custom_query($query, $bindings = []) {
        $result = $this->db->query($query, $bindings);
        return $result->result_array();
    }

    /**
     * Join tables dynamically.
     * 
     * @param string $table
     * @param array $joins
     * @param array $conditions
     * @param string $order_by
     * @param string $order_direction
     * @return array
     */
    public function get_with_joins($table, $joins = [], $conditions = [], $order_by = '', $order_direction = 'ASC') {
        $this->db->from($table);

        foreach ($joins as $join_table => $on_condition) {
            $this->db->join($join_table, $on_condition);
        }

        if (!empty($conditions)) {
            $this->db->where($conditions);
        }

        if ($order_by) {
            $this->db->order_by($order_by, $order_direction);
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Fetches data from a specified table based on given conditions.
     *
     * @param string $table The name of the database table.
     * @param array $where Associative array of conditions (e.g., ['id' => 1]).
     * @param bool $single If true, returns a single row; otherwise, returns multiple rows.
     * @return array|bool Returns an associative array (single/multiple rows) on success, or false on failure.
     */
    public function get_where($table, $where = [], $single = false)
    {
        if (empty($table) || !is_array($where) || empty($where)) {
            log_message('error', 'get_where() called with invalid parameters.');
            return false;
        }

        $query = $this->db->get_where($table, $where);

        if (!$query) {
            log_message('error', 'Database query failed: ' . $this->db->last_query());
            return false;
        }

        return $single ? $query->row_array() : $query->result_array();
    }


    public function get_groupMembers($college_id=null,$created_by=null){
        $this->db->select('COALESCE(count(mg.student_id), 0) AS students, g.id, g.group_name, g.group_expiry, g.created_at');
        $this->db->from('groups as g');
        $this->db->join(TABLE_MEMGROUPS . ' AS mg', 'g.id = mg.group_id AND (mg.college_id = ' . $this->db->escape($college_id) . ' OR ' . $this->db->escape($college_id) . ' IS NULL)', 'LEFT');
        
        if(is_array($created_by) && !empty($created_by)){
            $this->db->where_in('g.created_by', $created_by);
        }elseif($created_by != null){
            $this->db->where('g.created_by', $created_by);
        }

        $this->db->where('g.is_active', 1);
        $this->db->where('g.college_id',$college_id);
        $this->db->group_by('g.id, g.group_name, g.group_expiry, g.created_at');
        return $this->db->get()->result_array();
    }


    public function get_paginated($table, $conditions = [], $limit = 10, $offset = 0) {
        $this->db->where($conditions);
        $this->db->limit($limit, $offset);
        return $this->db->get($table)->result_array();
    }
    
    public function count_all($table, $conditions = []) {
        $this->db->where($conditions);
        return $this->db->count_all_results($table);
    }

            /**
     * Get maximum value of a field in a table with optional conditions
     *
     * @param string $table Table name
     * @param string $field Field to get max value from
     * @param array $where Optional where conditions
     * @return mixed Maximum value or NULL if no results
     */
    public function get_max($table, $field, $where = []) {
        $this->db->select_max($field);
        
        if (!empty($where)) {
            $this->db->where($where);
        }
        
        $query = $this->db->get($table);
        $result = $query->row();
        
        return $result ? $result->$field : 0;
    }

    /**
     * Get the sum of a column for rows matching the given conditions.
     *
     * @param string $table The name of the table.
     * @param array $conditions The conditions for the query.
     * @param string $column The column to sum.
     * @return int The sum of the column.
     */
    public function get_sum($table, $conditions = [], $column) {
        $this->db->select_sum($column);
        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                if (is_array($value)) {
                    // Use WHERE IN if the value is an array
                    $this->db->where_in($key, $value);
                } else {
                    // Otherwise, apply the regular WHERE condition
                    $this->db->where($key, $value);
                }
            }
        }
        $query = $this->db->get($table);
        $result = $query->row();
        return isset($result->$column) ? $result->$column : 0;
    }

}

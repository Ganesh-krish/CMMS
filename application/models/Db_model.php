<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class db_model extends CI_Model {

    public function __construct() { 
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
                    $this->db->where_in($key, $value);
                } else {
                    $this->db->where($key, $value);
                }
            }
        }
        if ($order_by) {
            $this->db->order_by($order_by, $order_direction);
        }
        $query = $this->db->get();
        return $query->result_array();
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
     * Bulk upsert (insert or update) records in a table.
     * 
     * @param string $table
     * @param array $data
     * @param array $unique_columns
     * @return bool
     */
    // Function to bulk upsert data into a table

    public function bulk_upsert($table, $data, $unique_columns) {
        $columns = array_keys($data[0]);
        
        $update_columns = array_diff($columns, $unique_columns);
        $update_str = '';
        foreach ($update_columns as $col) {
            $update_str .= "`$col` = VALUES(`$col`), ";
        }
        $update_str = rtrim($update_str, ', ');
        
        $sql = "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES ";
        $values = array();
        foreach ($data as $row) {
            $val = array();
            foreach ($row as $v) {
                $val[] = $this->db->escape($v);
            }
            $values[] = '(' . implode(', ', $val) . ')';
        }
        
        $sql .= implode(', ', $values);
        $sql .= " ON DUPLICATE KEY UPDATE " . $update_str;
        
        $this->db->query($sql);
        
        return true;
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
                    $this->db->where_in($key, $value);
                } else {
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
    public function get_with_joins($table, $select = '*',$joins = [], $conditions = [], $order_by = '', $order_direction = 'ASC',$group_by = '',$limit = null, $offset = null) {
         // SELECT
         $this->db->select($select);
        $this->db->from($table);

        foreach ($joins as $join_table => $on_condition) {
            $this->db->join($join_table, $on_condition,'left');
        }

        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                if (is_array($value)) {
                    $this->db->where_in($key, $value);
                } else {
                    $this->db->where($key, $value);
                }
            }
        }
        
        if (!empty($group_by)) {
        $this->db->group_by($group_by);
        }

        if ($order_by) {
            $this->db->order_by($order_by, $order_direction);
        }

        if (!empty($limit)) {
        $this->db->limit((int)$limit, (int)$offset);
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    // Function to get distinct values
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


   public function get_subscription_count($college_id){

    // Student table where college_id and (user_token and external_id) is not null
    $student_count = $this->db->where(['college_id' => $college_id, 'user_token !=' => null, 'external_id !=' => null])
                              ->count_all_results(TABLE_STUDENT);

    return $student_count; // Return the count

   }

    public function get_groupMembers($college_id=null,$created_by=null){
        return $this->get_groupMembers_test($college_id, $created_by);
    }

    public function get_groupMembers_test($college_id=null,$created_by=null){
        $this->db->select('COALESCE(count(mg.student_id), 0) AS students, g.id, g.name, g.group_expiry, g.created_at');
        $this->db->from('groups as g');
        $this->db->join(TABLE_MEMGROUPS . ' AS mg', 'g.id = mg.group_id AND (mg.college_id = ' . $this->db->escape($college_id) . ' OR ' . $this->db->escape($college_id) . ' IS NULL)', 'LEFT');

        if(is_array($created_by) && !empty($created_by)){
            $this->db->where_in('g.created_by', $created_by);
        }elseif($created_by != null){
            $this->db->where('g.created_by', $created_by);
        }

        $this->db->where('g.is_active', 1);
        $this->db->where('g.college_id',$college_id);
        $this->db->group_by('g.id, g.name, g.group_expiry, g.created_at');
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

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Salary_model extends MY_Model
{
    protected $table = "salary";

    public function getAllSalary($limit = null,$where = null){
        if ($limit != null){
            $this->db->limit($limit[1],$limit[0]);
        }
        if ($where != null){
            $this->db->where($where);
        }
        $this->db->order_by('id_salary', 'DESC');
        $this->db->select("$this->table.id as id_salary, $this->table.hard_salary, $this->table.subsidize, $this->table.id_user, user.fullname, user.birthday, user.gender, user.id_team, team.id_department");
        $this->db->join('user', 'user.id = ' . $this->table . '.id_user', 'inner');
        $this->db->join('team', 'team.id = user.id_team', 'left');
        $this->db->join('department', 'department.id = team.id_department', 'left');
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    public function countSalary($where = null){
        if ($where != null){
            $this->db->where($where);
        }
        $this->db->select("count(*) as total");
        $this->db->join('user', 'user.id = ' . $this->table . '.id_user', 'left');
        $this->db->join('team', 'team.id = user.id_team', 'left');
        $this->db->join('department', 'department.id = team.id_department', 'left');
        $query = $this->db->get($this->table);
        return (int)$query->first_row('array')['total'];
    }

    public function getTotalSalaryOfUser($id){
        $this->db->select("(hard_salary + subsidize) as totalSalary");
        $salary = $this->db->get_where($this->table, array('id_user'  =>  $id),1)->first_row('array');
        return (float)$salary['totalSalary'];
    }

}

/* End of file .php */
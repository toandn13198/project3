<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model
{
    protected $table = "user";

    public function getAllUser($limit = null,$where = null){
        if ($limit != null){
            $this->db->limit($limit[1],$limit[0]);
        }
        if ($where != null){
            $this->db->where($where);
        }
        $this->db->order_by('id', 'DESC');
        $this->db->select('user.*, team.id_department, team.name as name_team, department.name as name_department');
        $this->db->join('team', 'team.id = ' . $this->table . '.id_team', 'left');
        $this->db->join('department', 'department.id = team.id_department', 'left');
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    public function countUser($where = null){
        if ($where != null){
            $this->db->where($where);
        }
        $this->db->select('count(*) as total');
        $this->db->join('team', 'team.id = ' . $this->table . '.id_team', 'left');
        $this->db->join('department', 'department.id = team.id_department', 'left');
        $query = $this->db->get($this->table);
        return (int)$query->first_row('array')['total'];
    }

}

/* End of file .php */
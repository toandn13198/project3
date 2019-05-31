<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Explanation_model extends MY_Model
{
    protected $table = "explanation";

    public function getExplanation($id){
        $this->db->select('explanation.*, user.fullname as approver_fullname');
        $this->db->where('explanation.id', $id);
        $this->db->join('user', 'user.id = ' . $this->table . '.approver', 'left');
        $query = $this->db->get($this->table);
        return $query->first_row('array');
    }

    public function getRequestExplanation($limit = null, $where = null){
        if ($limit != null){
            $this->db->limit($limit[1],$limit[0]);
        }
        if ($where != null){
            $this->db->where($where);
        }
        $this->db->order_by('id', 'DESC');
        $this->db->select('explanation.*, user.fullname, user.image');
        $this->db->join('user', 'user.id = explanation.id_user', 'inner');
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    public function countRequestExplanation($where = null){
        if ($where != null){
            $this->db->where($where);
        }
        $this->db->select('count(*) as total');
        $this->db->join('user', 'user.id = explanation.id_user', 'inner');
        $query = $this->db->get($this->table);
        return (int)$query->first_row('array')['total'];
    }
}

/* End of file .php */
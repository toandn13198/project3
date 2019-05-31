<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vacation_model extends MY_Model
{
    protected $table = "vacation";

    public function getVacation($id){
        $this->db->select('vacation.*, handover_user.fullname as handover_fullname, user.fullname as user_fullname');
        $this->db->where('vacation.id', $id);
        $this->db->join('user as handover_user', 'handover_user.id = ' . $this->table . '.handover_id', 'left');
        $this->db->join('user as user', 'user.id = ' . $this->table . '.id_user', 'left');
        $query = $this->db->get($this->table);
        return $query->first_row('array');
    }

    public function getRequestVacation($limit = null, $where = null){
        if ($limit != null){
            $this->db->limit($limit[1],$limit[0]);
        }
        if ($where != null){
            $this->db->where($where);
        }
        $this->db->order_by('id', 'DESC');
        $this->db->select('vacation.*, user.fullname, user.image');
        $this->db->join('user', 'user.id = ' . $this->table . '.id_user', 'inner');
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    public function countRequestVacation($where = null){
        if ($where != null){
            $this->db->where($where);
        }
        $this->db->select('count(*) as total');
        $this->db->join('user', 'user.id = ' . $this->table . '.id_user', 'inner');
        $query = $this->db->get($this->table);
        return (int)$query->first_row('array')['total'];
    }

    public function getVacationInMonth($month,$where = NULL, $order_by = NULL){
        $this->db->group_start();
        $this->db->where("DATE_FORMAT(`date_from`,'%Y-%m') = '$month' OR DATE_FORMAT(`date_from`,'%Y-%m') = '$month'");
        $this->db->group_end();
        if ($where != null){
            $this->db->where($where);
        }
        if ($order_by != null){
            $this->db->order_by($order_by[0],$order_by[1]);
        }
        return $this->db->get($this->table)->result_array();
    }

}

/* End of file .php */
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Team_model extends MY_Model
{
    protected $table = "team";

    public function getAllTeam($limit = null, $where = null)
    {
        if ($limit != null){
            $this->db->limit($limit[1],$limit[0]);
        }
        if ($where != null){
            $this->db->where($where);
        }
        $this->db->order_by('id_team', 'DESC');
        $this->db->select("$this->table.id as id_team, $this->table.name as name_team, $this->table.leader as id_leader, $this->table.id_department, department.name as name_department, user.fullname as name_leader");
        $this->db->join('user', 'user.id = ' . $this->table . '.leader', 'left');
        $this->db->join('department', 'department.id = ' . $this->table . '.id_department', 'left');
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    //except is id user that you want to get although id_team is't null
    public function getUsersNoTeam($except = null){
        $this->db->where(array('id_team'    =>  NULL));
        if($except != null){
            $this->db->or_where('id',$except);
        }
        $query = $this->db->get('user');
        return $query->result_array();
    }

    public function getUsersOfTeam($id = NULL){
        if ($id == NULL)
            return array();
        $this->db->where(array('id_team'    =>  $id));
        $query = $this->db->get('user');
        return $query->result_array();
    }

    public function getUsersCanOnTeam($id){
        $this->db->where(array('id_team'    =>  NULL));
        $this->db->or_where('id_team',$id);
        $query = $this->db->get('user');
        return $query->result_array();
    }

    public function isLeader($id_team, $id_member){
        $where = array(
            'leader'    =>  $id_member,
            'id'    =>  $id_team
        );
        $count = $this->count($where);
        return ($count == 1) ? true : false;
    }

    public function setMember($listId = null, $id_team = NULL){
        $data = array();
        foreach ($listId as $id){
            $data[] = array(
                'id_team'   =>  $id_team,
                'id'    =>  $id
            );
        }
        if(empty($data)){
            return false;
        }
        $this->db->update_batch('user', $data, 'id');
    }

    public function unsetMember($listId = null){
        $data = array();
        foreach ($listId as $id){
            $data[] = array(
                'id_team'   =>  NULL,
                'id'    =>  $id
            );
        }
        if(empty($data)){
            return false;
        }
        $this->db->update_batch('user', $data, 'id');
    }


}

/* End of file .php */
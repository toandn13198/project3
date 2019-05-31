<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Model extends CI_Model{
    protected $table;
    protected $primary_key = "id";

    public function __construct()
    {
        $this->load->database();
    }

    public function getAll($where = null, $order_by = null, $limit = null)
    {
        if ($limit != null){
            $this->db->limit($limit[1],$limit[0]);
        }
        if ($order_by != null){
            $this->db->order_by($order_by[0], $order_by[1]);
        }
        if ($where != null){
            $this->db->where($where);
        }
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    public function getWhere($where = null, $order_by = null)
    {
        if ($where != null){
            $this->db->where($where);
        }
        if ($order_by != null){
            $this->db->order_by($order_by[0], $order_by[1]);
        }
        $this->db->limit(1,0);
        $query = $this->db->get($this->table);
        return $query->first_row('array');
    }

    public function insert($data = array())
    {
        $insert = $this->db->insert($this->table, $data);
        return ($insert) ? $this->db->insert_id() : false;
    }

    public function update($data = array(), $id)
    {
        $update = $this->db->update($this->table, $data, array( $this->primary_key => $id));
        return $update ? true : false;
    }

    public function delete($id)
    {
        $delete = $this->db->delete($this->table, array( $this->primary_key => $id));
        return $delete ? true : false;
    }

    public function checkUnique($where){
        $query = $this->db->get_where($this->table, $where);
        return ($query->num_rows() >= 1) ? false : true;
    }

    public function count($where = null){
        if ($where != null){
            $this->db->where($where);
        }
        $this->db->select("count(*) as total");
        $query = $this->db->get($this->table);
        return (int)$query->first_row('array')['total'];
    }

    public function getDateTime(){
        $this->db->select("now() as dateTime");
        return $this->db->get()->first_row('array')['dateTime'];
    }

    public function deleteWhere($where){
        $this->db->where($where);
        $this->db->delete($this->table);
    }
}
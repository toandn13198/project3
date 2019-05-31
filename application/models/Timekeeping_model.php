<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timekeeping_model extends MY_Model
{
    protected $table = "timekeeping";

    public function getAttendanceHistory($where = null){
        $sql = "SELECT id, min(checkin) as checkin, max(checkout) as checkout, sum(deduction) as deduction, status, id_user from timekeeping";
        if ($where != null){
            $sql .= " WHERE " . $where;
        }
        $sql .= " GROUP BY IFNULL(DATE_FORMAT(checkin,'%Y-%m-%d'), DATE_FORMAT(checkout,'%Y-%m-%d'))";
        $query = $this->db->query($sql);
        return ($query) ? $query->result_array() : null;
    }

}

/* End of file .php */
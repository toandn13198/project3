<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('asset')){
    function asset($dir){
        return base_url() . 'public/' . $dir;
    }
}

if (!function_exists('convertToArrayId')){
    function convertToArrayId($array){
        $arrayId = array();
        foreach ($array as $element){
            $arrayId[] = $element['id'];
        }
        return $arrayId;
    }
}


if (!function_exists('checkAttendance')){
    function checkAttendance(){
        $ci=& get_instance();
        $ci->load->model('timekeeping_model', 'timekeeping');
        $where = "id_user = " . $ci->session->id . " AND  DATE_FORMAT(checkin,'%Y-%m-%d') = DATE_FORMAT(now(),'%Y-%m-%d')";
        $timekeeping = $ci->timekeeping->getWhere($where, array('checkin','DESC'));
        return ($timekeeping != NULL && $timekeeping['status'] != 1) ? 0 : 1;
    }
}

if (!function_exists('convertToHoursMins')){
    function convertMinutesToHours($minutes) {
        if($minutes == null){
            return null;
        }
        if($minutes == 0){
            return '00:00';
        }
        if ($minutes < 60) {
            $time =  '00:' . $minutes;
        }else{
            $hours = floor($minutes / 60);
            $minute = $minutes - 60*($hours);
            $time = $hours . ':' . $minute;
        }
        return $time;
    }
}

if (!function_exists('redirectPreUrl')){
    function redirectPreUrl($url) {
        $ci=& get_instance();
        $ci->load->library('user_agent');
        if ($ci->agent->referrer()){
            redirect($ci->agent->referrer());
        }else{
            redirect($url);
        }
    }
}

if (!function_exists('dayNameOfWeek')){
    function dayNameOfWeek($date) {
        $newDayName = '';
        $dayOfWeek  = date('w', strtotime($date));
        switch ($dayOfWeek) {
            case 1:
                $newDayName = 'Thứ hai';
                break;
            case 2:
                $newDayName = 'Thứ ba';
                break;
            case 3:
                $newDayName = 'Thứ tư';
                break;
            case 4:
                $newDayName = 'Thứ năm';
                break;
            case 5:
                $newDayName = 'Thứ sáu';
                break;
            case 6:
                $newDayName = 'Thứ bảy';
                break;
            case 0:
                $newDayName = 'Chủ nhật';
                break;
        }
        return $newDayName;
    }
}

if (!function_exists('minusDatetimeToHour')){
    function minusDatetimeToHour($start, $end) {
        $dteStart = new DateTime($start);
        $dteEnd   = new DateTime($end);
        $total_minutes = 0;
        if($dteStart < $dteEnd) {
            $dteDiff = $dteStart->diff($dteEnd);
            $total_time = $dteDiff->format("%H:%I");
            $arr_time = explode(':', $total_time);
            $total_minutes = ((int)$arr_time[0] * 60) + (int)$arr_time[1];
        }
        return $total_minutes;
    }
}

if (!function_exists('kickUser')){
    function kickUser($id_user) {
        if (!$id_user) return false;
        $ci = & get_instance();
        $strId = implode(',', $id_user);
        $sqlId = "select access_id from user where id in (" . $strId . ")";
        $access_id = $ci->db->query($sqlId)->result_array();
        $str = "'";
        foreach ($access_id as $key => $value){
            if ($value == NULL) continue;
            $str .= implode("','", json_decode($value['access_id'], true)) . "'". ((next($access_id)) ? ",'" : "");
        }
        $ci->db->query('delete from ci_sessions where id in (' . $str . ')');
        $ci->db->query("UPDATE user SET access_id = NULL WHERE  id in (" . $strId . ")");
    }
}

if (!function_exists('isLeader')){
    function isLeader() {
        $ci = & get_instance();
        return $ci->session->is_leader;
    }
}

if (!function_exists('isAdmin')){
    function isAdmin() {
        $ci = & get_instance();
        return ($ci->session->role == 3) ? true : false;
    }
}

if (!function_exists('isHR')){
    function isHR() {
        $ci = & get_instance();
        return ($ci->session->role == 2) ? true : false;
    }
}

if (!function_exists('getMyId')){
    function getMyId() {
        $ci = & get_instance();
        return $ci->session->id;
    }
}

if (!function_exists('haveLeader')){
    function haveLeader() {
        $ci = & get_instance();
        $leader = $ci->db->get_where('team',array('id'  => $ci->session->id_team))->first_row('array');
        return ($leader) ? (int)$leader['leader'] : false;
    }
}

if (!function_exists('totalDayNameInMonth')){
    function totalDayNameInMonth($dayName,$month,$year) {
        $day=0;
        $total_days=cal_days_in_month(CAL_GREGORIAN, $month, $year);
        for($i=1;$i<=$total_days;$i++)
            if(date('N',strtotime($year.'-'.$month.'-'.$i))==$dayName)
                $day++;
        return $day;
    }
}

if (!function_exists('workingDay')){
    function workingDay($month,$year) {
        $totalDayInMonth = cal_days_in_month(CAL_GREGORIAN,$month,$year);
        $sunday = totalDayNameInMonth(7,$month,$year);
        $saturday = totalDayNameInMonth(6,$month,$year);
        return $totalDayInMonth - $sunday - ($saturday/2);
    }
}

if (!function_exists('realWorkingDay')){
    function realWorkingDay($total_minutes) {
        if ($total_minutes > 300) {
            $realDay = (($total_minutes / 60) - 1.5) / 8;
        }else{
            $realDay = ($total_minutes / 60) / 8;
        }
        if ($realDay >= 0.7) {
            $realDay = 1;
        } elseif ($realDay >= 0.3) {
            $realDay = 0.5;
        } else {
            $realDay = 0;
        }
        return $realDay;
    }
}



?>
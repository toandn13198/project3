<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Salary extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('salary_model', 'salary');
        $this->load->model('user_model', 'user');
        $this->load->model('department_model', 'department');
        $this->load->model('team_model', 'team');
        $this->load->model('timekeeping_model', 'timekeeping');
        $this->load->model('vacation_model', 'vacation');
    }

    public function index(){
        if(!(isAdmin() || isHR()))
            redirect(base_url('user/dashboard_user'));
        $config['params'] = array();
        $where = array();
        $teams = array();
        $departments = array();
        if ($this->input->get('employee-type') == 2) {
            $where['user.id_team'] = NULL;
            $config['params']['employee-type'] = $this->input->get('employee-type');
        }else{
            $config['params']['employee-type'] = $this->input->get('employee-type');
            $departments = $this->department->getAll();
            $idDepartment = convertToArrayId($departments);
            if (in_array($this->input->get('department'), $idDepartment)){
                $teams = $this->team->getAll(array('id_department' =>  $this->input->get('department')));
                $where['team.id_department'] = $this->input->get('department');
                $config['params']['department'] = $this->input->get('department');
            }else{
                $teams = array();
            }
            $idTeam = convertToArrayId($teams);
            if (in_array($this->input->get('team'), $idTeam)) {
                $where['user.id_team'] = $this->input->get('team');
                $config['params']['team'] = $this->input->get('team');
            }
            if ($this->input->get('employee-type') == 1)
                $where['user.id_team <>'] = NULL;
        }

        if($this->input->get('keyword') != null) {
            $where['user.fullname like'] = '%' . $this->input->get('keyword') . '%';
            $config['params']['keyword'] = $this->input->get('keyword');
        }
        $limit = 6;
        $config['total'] = $this->salary->countSalary($where);
        $config['limit'] = $limit;
        $config['page_current'] = ($this->input->get('page') != null) ? $this->input->get('page') : 1;
        $config['url'] = base_url('salary/index/');
        $this->load->library('paginator',$config);
        $pagi = $this->paginator;
        $page = $this->paginator->getPage();
        $start = ($page-1)*$limit;
        $listSalary = $this->salary->getAllSalary(array($start,$limit),$where);
        $this->render('page.salary_list', compact('listSalary','pagi', 'departments', 'teams'));
    }

    public function add(){
        if(!(isAdmin() || isHR()))
            redirect(base_url('user/dashboard_user'));
        $users = $this->user->getAll('id not in (select id_user from salary)', array('id', 'DESC'));
        $this->render('page.salary_add', compact('users'));
    }

    public function insert(){
        if(!(isAdmin() || isHR()))
            redirect(base_url('user/dashboard_user'));
        $data = array(
            'hard_salary'   =>  $this->input->post('hard_salary'),
            'subsidize'   =>  $this->input->post('subsidize'),
            'id_user'   =>  $this->input->post('user'),
        );
        $this->salary->insert($data);
        redirect(base_url('salary'));
    }

    public function edit($id){
        if(!(isAdmin() || isHR()))
            redirect(base_url('user/dashboard_user'));
        $salary = $this->salary->getWhere(array('id'    =>  $id));
        $users = $users = $this->user->getAll('id not in (select id_user from salary) or id = ' . $salary['id_user'] , array('id', 'DESC'));
        $this->render('page.salary_edit', compact('salary', 'users'));
    }

    public function update(){
        if(!(isAdmin() || isHR()))
            redirect(base_url('user/dashboard_user'));
        $data = array(
            'hard_salary'   =>  $this->input->post('hard_salary'),
            'subsidize'   =>  $this->input->post('subsidize'),
            'id_user'   =>  $this->input->post('user'),
        );
        $this->salary->update($data, $this->input->post('id'));
        redirect(base_url('salary'));
    }

    public function delete($id){
        if(!(isAdmin() || isHR()))
            redirect(base_url('user/dashboard_user'));
        $this->salary->delete($id);
        redirect(base_url('salary'));
    }

    public function salaryHistory(){
        $data = array();
        if ($this->input->get('month')){
            $now = $this->input->get('month');
            $now = explode("-", $now);
            $month = $now[0];
            $year = $now[1];
            $now = $year . '-' . $month;
            $today = $this->salary->getDateTime();
            $monthYear = date('Y-m', strtotime($today));
            if ($now <= $monthYear) {
                $str = $year . '-' .$month;
                $total_days=cal_days_in_month(CAL_GREGORIAN, $month, $year);
                $where = array(
                    'id_user'   => $this->session->id,
                    'status'    =>  1
                );
                $vacation = $this->vacation->getVacationInMonth($str,$where);
                $dayVacation = 0;
                foreach ($vacation as $item){
                    $date_from = new DateTime($item['date_from']);
                    $date_to  = new DateTime($item['date_to']);
                    $dteStart   = new DateTime($str . '-01' . ' 08:30:00');
                    $dteEnd   = new DateTime($str . "-$total_days" . '18:00:00');
                    if($date_from >= $dteStart && $date_to <=  $dteEnd){
                        $dayVacation += $item['day_number'];
                    }else{
                        if($date_from < $dteStart && ($date_to >= $dteStart && $date_to <=  $dteEnd)){
                            $begin =$dteStart;
                            $end =$date_to;
                        }elseif($date_to > $dteEnd && ($date_from >= $dteStart && $date_from <=  $dteEnd)){
                            $begin =$date_from;
                            $end =$dteEnd;
                        }
                        $dteDiff = $begin->diff($end);
                        $dateTime = $dteDiff->format("%D %h:%i");
                        $dateTime = explode(" ", $dateTime);
                        $dayVacation += (int)$dateTime[0];
                        $time = explode(':', $dateTime[1]);
                        $minutes = $time[0]*60 + $time[1];
                        $dayVacation += realWorkingDay($minutes);
                    }
                }
                $data = $this->salary->getWhere(array('id_user' =>  $this->session->id));
                if (!$data){
                    $this->render('page.salary_history');
                    return false;
                }
                $data['dayVacation'] = $dayVacation;
                $workingDay =  workingDay($month,$year);
                $data['workingDay'] =  $workingDay;
                $attendance = $this->timekeeping->getAttendanceHistory("id_user = " . $this->session->id . " AND (DATE_FORMAT(checkin,'%Y-%m') = '$str' OR DATE_FORMAT(checkout,'%Y-%m') = '$str' )");
                $data['realday'] = 0;
                $data['deduction'] = 0;
                foreach ($attendance as $key2 => $value2) {
                    if ($value2['checkin'] != NULL){
                        $date = date("Y-m-d", strtotime($value2['checkin']));
                    }else{
                        $date = date("Y-m-d", strtotime($value2['checkout']));
                    }
                    $lastAttendance = $this->timekeeping->getWhere("id_user = " .$this->session->id . " AND  DATE_FORMAT(checkin,'%Y-%m-%d') = DATE_FORMAT('" . $value2['checkin'] . "','%Y-%m-%d') and checkout is null", array('checkin', 'DESC'));
                    //missing checkout
                    if ($lastAttendance) {
                        if(date('w', strtotime($date)) == 6){
                            $strEnd = $date . " 12:00:59";
                        }else{
                            $strEnd = $date . " 18:00:59";
                        }
                    }else{
                        $strEnd = $value2['checkout'] ;
                    }
                    //missing checkin
                    if ($value2['checkin'] === NULL){
                        $strStart = $date . ' 08:30:00';
                    }else{
                        $strStart = $value2['checkin'];
                    }
                    //check trong gio hanh chinh
                    $time_start = date('H:i:s', strtotime($strStart));
                    if (strtotime($time_start) < strtotime('08:30:00')){
                        $time_start = $date . ' 08:30:00';
                    }else{
                        $time_start = $strStart;
                    }
                    $time_end = date('H:i:s', strtotime($strEnd));
                    if (strtotime($time_end) > strtotime('18:00:00')){
                        $time_end = $date . ' 18:00:00';
                    }else{
                        $time_end = $strEnd;
                    };
                    //caculate realDay
                    $realDay = 0;
                    $realDay = realWorkingDay(minusDatetimeToHour($time_start, $time_end));
                    if(date('w', strtotime($date)) == 6 && $realDay > 0.5) {
                        $realDay = 0.5;
                    }
                    $data['realday'] += $realDay;
                    $data['deduction'] += $value2['deduction'];
                }
                $data['realSalary'] = ((($data['hard_salary']+$data['subsidize'])/$workingDay)*($data['realday']+$data['dayVacation'])) - $data['deduction'];
            }
        }
        $this->render('page.salary_history', $data);

    }

    public function statistics(){
        if(!(isAdmin() || isHR()))
            redirect(base_url('user/dashboard_user'));
        $where = array();
        $teams = array();
        $departments = array();
        if ($this->input->get('employee-type') == 2){
            $where['id_team'] = NULL;
        }else{
            $departments = $this->department->getAll();
            $idDepartment = convertToArrayId($departments);
            if (in_array($this->input->get('department'), $idDepartment)){
                $where['id_department'] = $this->input->get('department');
                $teams = $this->team->getAll(array('id_department' =>  $this->input->get('department')));
            }
            $idTeam = convertToArrayId($teams);
            if (in_array($this->input->get('team'), $idTeam)) {
                $where['id_team']   =   $this->input->get('team');
            }
        }
        $data = array();
        if ($this->input->get('month')) {
            $now = $this->input->get('month');
            $now = explode('-', $now);
            $month = $now[0];
            $year = $now[1];
            $now = $year . '-' . $month;
            $today = $this->timekeeping->getDateTime();
            $monthYear = date('Y-m', strtotime($today));
            if ($now < $monthYear) {
                $data = $this->salary->getAllSalary(NULL,$where);
                $total_days=cal_days_in_month(CAL_GREGORIAN, $month, $year);
                $workingDay =  workingDay($month,$year);
                foreach ($data as $key => $value){
                    $attendance = $this->timekeeping->getAttendanceHistory("id_user = " . $value['id_user'] . " AND (DATE_FORMAT(checkin,'%Y-%m') = '$year-$month' OR DATE_FORMAT(checkout,'%Y-%m') = '$year-$month' )");
                    $data[$key]['realday'] = 0;
                    $data[$key]['deduction'] = 0;
                    foreach ($attendance as $key2 => $value2) {
                        if ($value2['checkin'] != NULL){
                            $date = date("Y-m-d", strtotime($value2['checkin']));
                        }else{
                            $date = date("Y-m-d", strtotime($value2['checkout']));
                        }
                        $lastAttendance = $this->timekeeping->getWhere("id_user = " . $value['id_user'] . " AND  DATE_FORMAT(checkin,'%Y-%m-%d') = DATE_FORMAT('" . $value2['checkin'] . "','%Y-%m-%d') and checkout is null", array('checkin', 'DESC'));
                        //missing checkout
                        if ($lastAttendance) {
                            if(date('w', strtotime($date)) == 6){
                                $strEnd = $date . " 12:00:59";
                            }else{
                                $strEnd = $date . " 18:00:59";
                            }
                        }else{
                            $strEnd = $value2['checkout'] ;
                        }
                        //missing checkin
                        if ($value2['checkin'] === NULL){
                            $strStart = $date . ' 08:30:00';
                        }else{
                            $strStart = $value2['checkin'];
                        }
                        $time_start = date('H:i:s', strtotime($strStart));
                        if (strtotime($time_start) < strtotime('08:30:00')){
                            $time_start = $date . ' 08:30:00';
                        }else{
                            $time_start = $strStart;
                        }
                        $time_end = date('H:i:s', strtotime($strEnd));
                        if (strtotime($time_end) > strtotime('18:00:00')){
                            $time_end = $date . ' 18:00:00';
                        }else{
                            $time_end = $strEnd;
                        };
                        //caculate realDay
                        $realDay = 0;
                        $realDay = realWorkingDay(minusDatetimeToHour($time_start, $time_end));
                        if(date('w', strtotime($date)) == 6 && $realDay > 0.5) {
                            $realDay = 0.5;
                        }
                        $data[$key]['realday'] += $realDay;
                        $data[$key]['deduction'] += $value2['deduction'];
                    }
                    $vacation = $this->vacation->getVacationInMonth($now,array('status' =>  1, 'id_user'    =>  $value['id_user']));
                    $dayVacation = 0;
                    foreach ($vacation as $item){
                        $date_from = new DateTime($item['date_from']);
                        $date_to  = new DateTime($item['date_to']);
                        $dteStart   = new DateTime($now . '-01' . ' 08:30:00');
                        $dteEnd   = new DateTime($now . "-$total_days" . '18:00:00');
                        if($date_from >= $dteStart && $date_to <=  $dteEnd){
                            $dayVacation += $item['day_number'];
                        }else{
                            if($date_from < $dteStart && ($date_to >= $dteStart && $date_to <=  $dteEnd)){
                                $begin =$dteStart;
                                $end =$date_to;
                            }elseif($date_to > $dteEnd && ($date_from >= $dteStart && $date_from <=  $dteEnd)){
                                $begin =$date_from;
                                $end =$dteEnd;
                            }
                            $dteDiff = $begin->diff($end);
                            $dateTime = $dteDiff->format("%D %h:%i");
                            $dateTime = explode(" ", $dateTime);
                            $dayVacation += (int)$dateTime[0];
                            $time = explode(':', $dateTime[1]);
                            $minutes = $time[0]*60 + $time[1];
                            $dayVacation += realWorkingDay($minutes);
                        }
                    }
                    $data[$key]['realday'] += $dayVacation;
                    $data[$key]['realSalary'] = ((($value['hard_salary']+$value['subsidize'])/$workingDay)*$data[$key]['realday']) - $data[$key]['deduction'];
                }
            }
        }
        $this->render('page.salary_statistics', compact('data', 'departments', 'teams'));
    }

    public function detail(){
        if (!(isAdmin() || isHR()))
            redirect(base_url());
        $teams = array();
        $departments = array();
        if ($this->input->get('employee-type') == 2){
            $users = $this->user->getAll(array('id_team' =>  NULL));
        }else{
            $departments = $this->department->getAll();
            $idDepartment = convertToArrayId($departments);
            if (in_array($this->input->get('department'), $idDepartment)){
                $teams = $this->team->getAll(array('id_department' =>  $this->input->get('department')));
            }
            $idTeam = convertToArrayId($teams);
            if (in_array($this->input->get('team'), $idTeam)) {
                $users = $this->user->getAll(array('id_team' =>  $this->input->get('team')));
            }else{
                $users = array();
            }
        }
        $idUser = convertToArrayId($users);
        $id = $this->input->get('user');
        if (in_array($id, $idUser)) {
            if ($this->input->get('month')) {
                $now = $this->input->get('month');
                $now = explode('-', $now);
                $now = $now[1] . '-' . $now[0];
                $today = $this->timekeeping->getDateTime();
                $monthYear = date('Y-m', strtotime($today));
                if ($now <= $monthYear) {
                    $arrNow = explode("-", $now);
                    $month = $arrNow[1];
                    $year = $arrNow[0];
                    $str = $year . '-' .$month;
                    $total_days=cal_days_in_month(CAL_GREGORIAN, $month, $year);
                    $where = array(
                        'id_user'   => $id,
                        'status'    =>  1
                    );
                    $vacation = $this->vacation->getVacationInMonth($str,$where);
                    $dayVacation = 0;
                    foreach ($vacation as $item){
                        $date_from = new DateTime($item['date_from']);
                        $date_to  = new DateTime($item['date_to']);
                        $dteStart   = new DateTime($str . '-01' . ' 08:30:00');
                        $dteEnd   = new DateTime($str . "-$total_days" . '18:00:00');
                        if($date_from >= $dteStart && $date_to <=  $dteEnd){
                            $dayVacation += $item['day_number'];
                        }else{
                            if($date_from < $dteStart && ($date_to >= $dteStart && $date_to <=  $dteEnd)){
                                $begin =$dteStart;
                                $end =$date_to;
                            }elseif($date_to > $dteEnd && ($date_from >= $dteStart && $date_from <=  $dteEnd)){
                                $begin =$date_from;
                                $end =$dteEnd;
                            }
                            $dteDiff = $begin->diff($end);
                            $dateTime = $dteDiff->format("%D %h:%i");
                            $dateTime = explode(" ", $dateTime);
                            $dayVacation += (int)$dateTime[0];
                            $time = explode(':', $dateTime[1]);
                            $minutes = $time[0]*60 + $time[1];
                            $dayVacation += realWorkingDay($minutes);
                        }
                    }
                    $data = $this->salary->getWhere(array('id_user' =>  $id));
                    if (!$data){
                        $error = 'Nhân viên này không có lương!!!';
                        $this->render('page.salary_detail',compact('departments', 'teams', 'users', 'error'));
                        return false;
                    }
                    $data['dayVacation'] = $dayVacation;
                    $workingDay =  workingDay($month,$year);
                    $data['workingDay'] =  $workingDay;
                    $attendance = $this->timekeeping->getAttendanceHistory("id_user = " . $id . " AND (DATE_FORMAT(checkin,'%Y-%m') = '$year-$month' OR DATE_FORMAT(checkout,'%Y-%m') = '$year-$month' )");
                    $data['realday'] = 0;
                    $data['deduction'] = 0;
                    foreach ($attendance as $key2 => $value2) {
                        if ($value2['checkin'] != NULL){
                            $date = date("Y-m-d", strtotime($value2['checkin']));
                        }else{
                            $date = date("Y-m-d", strtotime($value2['checkout']));
                        }
                        $lastAttendance = $this->timekeeping->getWhere("id_user = " .$id . " AND  DATE_FORMAT(checkin,'%Y-%m-%d') = DATE_FORMAT('" . $value2['checkin'] . "','%Y-%m-%d') and checkout is null", array('checkin', 'DESC'));
                        //missing checkout
                        if ($lastAttendance) {
                            if(date('w', strtotime($date)) == 6){
                                $strEnd = $date . " 12:00:59";
                            }else{
                                $strEnd = $date . " 18:00:59";
                            }
                        }else{
                            $strEnd = $value2['checkout'] ;
                        }
                        //missing checkin
                        if ($value2['checkin'] === NULL){
                            $strStart = $date . ' 08:30:00';
                        }else{
                            $strStart = $value2['checkin'];
                        }
                        //check trong gio hanh chinh
                        $time_start = date('H:i:s', strtotime($strStart));
                        if (strtotime($time_start) < strtotime('08:30:00')){
                            $time_start = $date . ' 08:30:00';
                        }else{
                            $time_start = $strStart;
                        }
                        $time_end = date('H:i:s', strtotime($strEnd));
                        if (strtotime($time_end) > strtotime('18:00:00')){
                            $time_end = $date . ' 18:00:00';
                        }else{
                            $time_end = $strEnd;
                        };
                        //caculate realDay
                        $realDay = 0;
                        $realDay = realWorkingDay(minusDatetimeToHour($time_start, $time_end));
                        if(date('w', strtotime($date)) == 6 && $realDay > 0.5) {
                            $realDay = 0.5;
                        }
                        $data['realday'] += $realDay;
                        $data['deduction'] += $value2['deduction'];
                    }
                    $data['realSalary'] = ((($data['hard_salary']+$data['subsidize'])/$workingDay)*($data['realday']+$data['dayVacation'])) - $data['deduction'];
                }
            }
        }
        $this->render('page.salary_detail', compact('data', 'departments', 'teams', 'users'));
    }

}
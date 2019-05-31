<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Timekeeping extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('timekeeping_model', 'timekeeping');
        $this->load->model('deduction_model', 'deduction');
        $this->load->model('department_model', 'department');
        $this->load->model('team_model', 'team');
        $this->load->model('user_model', 'user');
        $this->load->model('salary_model', 'salary');
        $this->load->model('vacation_model', 'vacation');
        $this->load->model('explanation_model', 'explanation');
    }

    public function updateDeduction(){
        if (!(isAdmin() || isHR()))
            redirect(base_url());
        $result = $this->timekeeping->update(array('deduction'    =>  $this->input->post('deduction')), $this->input->post('id'));
        echo $result;
    }

    public function add(){
        if (!(isAdmin() || isHR()))
            redirect(base_url());
        $id_user = $this->input->post('id_user');
        $date = $this->input->post('date');
        $checkin = $this->input->post('checkin');
        $checkout = $this->input->post('checkout');
        $minus_amount = 0;
        $time_start = (strtotime($checkin) < strtotime('08:30')) ? '08:30' : $checkin;
        $total_minutes = ( strtotime($time_start) - strtotime('08:30') ) / 60;
        $deduction = $this->deduction->getWhere(array('start <'   =>  $total_minutes, 'end >='    =>  $total_minutes));
        if(!$deduction){
            $deduction = $this->deduction->getWhere(array('start <'   =>  $total_minutes),array('start', 'DESC'));
        }
        if($deduction){
            if($deduction['unit'] == 0){
                $minus_amount = $deduction['minus_amount'];
            }else{
                $month = Date('m', strtotime($date));
                $year = Date('Y', strtotime($date));
                $workingDay = workingDay($month,$year);
                $salary = $this->salary->getTotalSalaryOfUser($id_user);
                if (!$salary){
                    $this->session->set_flashdata('notification', array('type'  =>  'error', 'title'  =>  'Thêm thất bại!!!', 'message'    =>  'Không có lương'));
                    redirect(base_url());
                }
                $moneyofday = (float)($salary/$workingDay);
                $minus_amount = round($deduction['minus_amount']*$moneyofday, 0);
            }
        }
        $data = array(
            'checkin'   =>  $date . ' ' . $checkin,
            'checkout'   =>  $date . ' ' . $checkout,
            'deduction'   =>  $minus_amount,
            'status'   =>  '1',
            'id_user'   =>  $id_user,
        );
        $this->timekeeping->insert($data);
        redirectPreUrl(base_url('timekeeping/manage'));
    }

    public function checkIn(){
        $urlredirect = "";
        if (!(isAdmin() || isHR())){
            $urlredirect = "user/dashboard_user";
        }
        $now = $this->timekeeping->getDateTime();
        $vacation = $this->vacation->getWhere(array(
            "id_user"    =>   $this->session->id,
            "status"    =>   1,
            "date_from <="  =>  $now,
            "date_to >="  =>  $now
        ));
        if ($vacation){
            $this->session->set_flashdata('notification', array('type'  =>  'error', 'title'  =>  'CheckIn thất bại!!!', 'message'    =>  'Bạn đang trong thời gian nghỉ phép'));
            redirect(base_url($urlredirect));
        }
        $attendance = $this->timekeeping->getWhere("id_user = " . $this->session->id . " AND  DATE_FORMAT(checkin,'%Y-%m-%d') = DATE_FORMAT(now(),'%Y-%m-%d') ORDER BY checkin  DESC");
        if($attendance['status'] != 0 || $attendance == null){
            $data = array(
                'checkin'  =>  $now,
                'status'    =>  0,
                'id_user'   =>  $this->session->id
            );
            //get deduction
            $where = "id_user = " . $this->session->id . " AND  DATE_FORMAT(checkin,'%Y-%m-%d') = DATE_FORMAT(now(),'%Y-%m-%d')";
            //first check
            $minus_amount = 0;
            if ($this->timekeeping->count($where) == 0){
                $vacation = $this->vacation->getWhere(array(
                    "id_user"    =>   $this->session->id,
                    "status"    =>   1,
                    "date_from <="  => date("Y-m-d",strtotime($now)) . " 10:30:00" ,
                    "date_to >="  =>  date("Y-m-d",strtotime($now)) . " 10:30:00"
                ));
                if (!$vacation){
                    $strStart = date("Y-m-d",strtotime($now)) . " 08:30:00";
                    $total_minutes = minusDatetimeToHour($strStart,$now);
                    $deduction = $this->deduction->getWhere(array('start <'   =>  $total_minutes, 'end >='    =>  $total_minutes));
                    if(!$deduction){
                        $deduction = $this->deduction->getWhere(array('start <'   =>  $total_minutes),array('start', 'DESC'));
                    }
                    if($deduction){
                        if($deduction['unit'] == 0){
                            $minus_amount = $deduction['minus_amount'];
                        }else{
                            $month = Date('m', strtotime($now));
                            $year = Date('Y', strtotime($now));
                            $workingDay = workingDay($month,$year);
                            $salary = $this->salary->getTotalSalaryOfUser($this->session->id);
                            if (!$salary){
                                $this->session->set_flashdata('notification', array('type'  =>  'error', 'title'  =>  'CheckIn thất bại!!!', 'message'    =>  'Vui lòng có lương để checkin :)'));
                                redirect(base_url($urlredirect));
                            }
                            $moneyofday = (float)($salary/$workingDay);
                            $minus_amount = round($deduction['minus_amount']*$moneyofday, 0);
                        }
                    }
                }
            }
            $data['deduction'] = $minus_amount;
            $id = $this->timekeeping->insert($data);
            if($id){
                $this->session->set_flashdata('notification', array('type'  =>  'success', 'title'  =>  'CheckIn thành công!!!', 'message'    =>  'Bạn đã checkin vào lúc ' . date("h:i d-m-Y",strtotime($now))));
            }
        }else{
            $this->session->set_flashdata('notification', array('type'  =>  'error', 'title'  =>  'CheckIn thất bại!!!', 'message'    =>  'Bạn đã checkin trước đó vào lúc ' . date("h:i d-m-Y",strtotime($attendance['checkin']))));
        }
        redirect(base_url($urlredirect));
    }

    public function checkOut(){
        $urlredirect = "";
        if (!(isAdmin() || isHR())){
            $urlredirect = "user/dashboard_user";
        }
        $now = $this->timekeeping->getDateTime();
        $timekeeping = $this->timekeeping->getWhere("id_user = " . $this->session->id . " AND  DATE_FORMAT(checkin,'%Y-%m-%d') = DATE_FORMAT(now(),'%Y-%m-%d')", array('checkin','DESC'));
        if ($timekeeping != NULL && $timekeeping['status'] != 1){
            //calculate total minutes work in company
            $data = array(
                'checkout'  =>  $now,
                'status'    =>  1
            );
            $boo = $this->timekeeping->update($data, $timekeeping['id']);
            if($boo){
                $this->session->set_flashdata('notification', array('type'  =>  'success', 'title'  =>  'CheckOut thành công!!!', 'message'    =>  'Bạn đã checkout vào lúc ' . date("h:i d-m-Y",strtotime($now))));
            }
        }else{
            $this->session->set_flashdata('notification', array('type'  =>  'error', 'title'  =>  'CheckOut thất bại!!!', 'message'    =>  'Bạn không thể checkout!'));
        }
        redirect(base_url($urlredirect));
    }

    public function history(){
        $id = $this->session->id;
        $approvers = array();
        if((isLeader() || $this->session->id_team == NULL || !haveLeader()) && !isAdmin()){
            $approvers = $this->user->getAll(array('role'    =>  3));
        }
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
                if ($now == $monthYear) {
                    $total_days = (int)date('d', strtotime($today)) - 1;
                } else {
                    $total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                }
                $where = "id_user = $id AND (DATE_FORMAT(checkin,'%Y-%m') = '$now' OR DATE_FORMAT(checkout,'%Y-%m') = '$now' )";
                $dataTimekeeping = array();
                $attendance = $this->timekeeping->getAttendanceHistory($where);
                foreach ($attendance as $key => $value) {
                    if ($value['checkin'] != NULL){
                        $day = (int)date("d", strtotime($value['checkin']));
                        $date = date("Y-m-d", strtotime($value['checkin']));
                    }else{
                        $day = (int)date("d", strtotime($value['checkout']));
                        $date = date("Y-m-d", strtotime($value['checkout']));
                    }
                    $lastAttendance = $this->timekeeping->getWhere("id_user = " . $id . " AND  DATE_FORMAT(checkin,'%Y-%m-%d') = DATE_FORMAT('" . $value['checkin'] . "','%Y-%m-%d') and checkout is null", array('checkin', 'DESC'));
                    //missing checkout
                    if ($lastAttendance) {
                        if(date('w', strtotime($date)) == 6){
                            $strEnd = $date . " 12:00:59";
                        }else{
                            $strEnd = $date . " 18:00:59";
                        }
                        if ($value['id'] != $lastAttendance['id']) {
                            $attendance[$key]['status'] = 0;
                            $attendance[$key]['checkout'] = NULL;
                        }

                    } else {
                        $strEnd = $value['checkout'];
                    }
                    if ($value['checkin'] === NULL){
                        $strStart = $date . ' 08:30:00';
                    }else{
                        $strStart = $value['checkin'];
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
                    $total_minutes = minusDatetimeToHour(date('Y-m-d H:i',strtotime($strStart)),date('Y-m-d H:i',strtotime($strEnd)));
                    $attendance[$key]['total_minutes'] = $total_minutes;
                    //caculate realDay
                    $realDay = 0;
                    $realDay = realWorkingDay(minusDatetimeToHour($time_start, $time_end));
                    if(date('w', strtotime($date)) == 6 && $realDay > 0.5) {
                        $realDay = 0.5;
                    }
                    $attendance[$key]['realDay'] = $realDay;
                    //caculate total minutes late
                    $attendance[$key]['total_minutes_late']=0;
                    if ($value['checkin'] != NULL){
                        $strStart = $date . " 08:30:00";
                        $attendance[$key]['total_minutes_late'] = minusDatetimeToHour($strStart, $value['checkin']);
                    }
                    $attendance[$key]['day_name'] = dayNameOfWeek($date);
                    //check explanation
                    $explanation = $this->explanation->getWhere(array('date_explanation'  =>  $date, 'id_user'  =>  $id));
                    $attendance[$key]['id_explanation'] = ($explanation != NULL) ? $explanation['id'] : NULL;
                    $attendance[$key]['date'] = $date;
                    $dataTimekeeping[$day] = $attendance[$key];
                }
                $key_day = date("d", strtotime($today));
                unset($dataTimekeeping[$key_day]);
                for ($i = 1; $i <= $total_days; $i++){
                    $day = $year . '-' . $month . '-' . (($i<10) ? '0'.$i : $i);
                    if (!isset($dataTimekeeping[$i])){
                        $explanation = $this->explanation->getWhere(array('date_explanation'    =>  $day, 'id_user'    =>  $id));
                        $dataTimekeeping[$i] = array(
                            'id'    =>  NULL,
                            'day_name'  =>  dayNameOfWeek($day),
                            'checkin'   =>  NULL,
                            'checkout'  =>  NULL,
                            'deduction'  =>  NULL,
                            'status'    => NULL,
                            'id_explanation'    =>  ($explanation) ? $explanation['id'] : NULL,
                            'id_user'   =>  $id,
                            'total_minutes' =>  NULL,
                            'realDay'   =>  NULL,
                            'total_minutes_late'    =>  NULL,
                            'date'  =>  $day
                        );
                    }
                }
            }
        }
        $this->render('page.timekeeping_history', compact('dataTimekeeping', 'approvers'));
    }

    public function statistics(){
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
                    if ($now == $monthYear) {
                        $total_days = (int)date('d', strtotime($today)) - 1;
                    } else {
                        $total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                    }
                    $where = "id_user = $id AND (DATE_FORMAT(checkin,'%Y-%m') = '$now' OR DATE_FORMAT(checkout,'%Y-%m') = '$now' )";
                    $dataTimekeeping = array();
                    $attendance = $this->timekeeping->getAttendanceHistory($where);
                    foreach ($attendance as $key => $value) {
                        if ($value['checkin'] != NULL){
                            $day = (int)date("d", strtotime($value['checkin']));
                            $date = date("Y-m-d", strtotime($value['checkin']));
                        }else{
                            $day = (int)date("d", strtotime($value['checkout']));
                            $date = date("Y-m-d", strtotime($value['checkout']));
                        }
                        $lastAttendance = $this->timekeeping->getWhere("id_user = " . $id . " AND  DATE_FORMAT(checkin,'%Y-%m-%d') = DATE_FORMAT('" . $value['checkin'] . "','%Y-%m-%d') and checkout is null", array('checkin', 'DESC'));
                        //missing checkout
                        if ($lastAttendance) {
                            if(date('w', strtotime($date)) == 6){
                                $strEnd = $date . " 12:00:59";
                            }else{
                                $strEnd = $date . " 18:00:59";
                            }
                            if ($value['id'] != $lastAttendance['id']) {
                                $attendance[$key]['status'] = 0;
                                $attendance[$key]['checkout'] = NULL;
                            }

                        } else {
                            $strEnd = $value['checkout'];
                        }
                        if ($value['checkin'] === NULL){
                            $strStart = $date . ' 08:30:00';
                        }else{
                            $strStart = $value['checkin'];
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
                        $total_minutes = minusDatetimeToHour(date('Y-m-d H:i',strtotime($strStart)),date('Y-m-d H:i',strtotime($strEnd)));
                        $attendance[$key]['total_minutes'] = $total_minutes;
                        //caculate realDay
                        $realDay = 0;
                        $realDay = realWorkingDay(minusDatetimeToHour($time_start, $time_end));
                        if(date('w', strtotime($date)) == 6 && $realDay > 0.5) {
                            $realDay = 0.5;
                        }
                        $attendance[$key]['realDay'] = $realDay;
                        //caculate total minutes late
                        $attendance[$key]['total_minutes_late']=0;
                        if ($value['checkin'] != NULL){
                            $strStart = $date . " 08:30:00";
                            $attendance[$key]['total_minutes_late'] = minusDatetimeToHour($strStart, $value['checkin']);
                        }
                        $attendance[$key]['day_name'] = dayNameOfWeek($date);
                        //check explanation
                        $explanation = $this->explanation->getWhere(array('date_explanation'  =>  $date, 'id_user'  =>  $id, 'status'   =>  1));
                        $attendance[$key]['content_explanation'] = ($explanation != NULL) ? $explanation['content'] : NULL;
                        $attendance[$key]['date'] = $date;
                        $dataTimekeeping[$day] = $attendance[$key];
                    }
                    $key_day = date("d", strtotime($today));
                    unset($dataTimekeeping[$key_day]);
                    for ($i = 1; $i <= $total_days; $i++){
                        $day = $year . '-' . $month . '-' . (($i<10) ? '0'.$i : $i);
                        if (!isset($dataTimekeeping[$i])){
                            $explanation = $this->explanation->getWhere(array('date_explanation'    =>  $day, 'status'  =>  1, 'id_user'    =>  $id));
                            $dataTimekeeping[$i] = array(
                                'id'    =>  NULL,
                                'day_name'  =>  dayNameOfWeek($day),
                                'checkin'   =>  NULL,
                                'checkout'  =>  NULL,
                                'deduction'  =>  NULL,
                                'status'    => NULL,
                                'content_explanation'    =>  ($explanation) ? $explanation['content'] : NULL,
                                'id_user'   =>  $id,
                                'total_minutes' =>  NULL,
                                'realDay'   =>  NULL,
                                'total_minutes_late'    =>  NULL,
                                'date'  =>  $day
                            );
                        }
                    }
                }
            }
        }
        $this->render('page.timekeeping_statistics', compact('dataTimekeeping', 'departments', 'teams', 'users'));
    }

    public function manage(){
        if (!(isAdmin() || isHR()))
            redirect(base_url());
        $teams = array();
        $departments = array();
        if ($this->input->get('employee-type') == 2) {
            $users = $this->user->getAll(array('id_team' =>  NULL));
        }else {
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
                    if ($now == $monthYear) {
                        $total_days = (int)date('d', strtotime($today)) - 1;
                    } else {
                        $total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                    }
                    $where = "id_user = $id AND (DATE_FORMAT(checkin,'%Y-%m') = '$now' OR DATE_FORMAT(checkout,'%Y-%m') = '$now' )";
                    $dataTimekeeping = array();
                    $attendance = $this->timekeeping->getAttendanceHistory($where);
                    foreach ($attendance as $key => $value) {
                        if ($value['checkin'] != NULL){
                            $day = (int)date("d", strtotime($value['checkin']));
                            $date = date("Y-m-d", strtotime($value['checkin']));
                        }else{
                            $day = (int)date("d", strtotime($value['checkout']));
                            $date = date("Y-m-d", strtotime($value['checkout']));
                        }
                        $lastAttendance = $this->timekeeping->getWhere("id_user = " . $id . " AND  DATE_FORMAT(checkin,'%Y-%m-%d') = DATE_FORMAT('" . $value['checkin'] . "','%Y-%m-%d') and checkout is null", array('checkin', 'DESC'));
                        //missing checkout
                        if ($lastAttendance) {
                            if(date('w', strtotime($date)) == 6){
                                $strEnd = $date . " 12:00:59";
                            }else{
                                $strEnd = $date . " 18:00:59";
                            }
                            if ($value['id'] != $lastAttendance['id']) {
                                $attendance[$key]['status'] = 0;
                                $attendance[$key]['checkout'] = NULL;
                            }

                        } else {
                            $strEnd = $value['checkout'];
                        }
                        if ($value['checkin'] === NULL){
                            $strStart = $date . ' 08:30:00';
                        }else{
                            $strStart = $value['checkin'];
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
                        $total_minutes = minusDatetimeToHour(date('Y-m-d H:i',strtotime($strStart)),date('Y-m-d H:i',strtotime($strEnd)));
                        $attendance[$key]['total_minutes'] = $total_minutes;
                        //caculate realDay
                        $realDay = 0;
                        $realDay = realWorkingDay(minusDatetimeToHour($time_start, $time_end));
                        if(date('w', strtotime($date)) == 6 && $realDay > 0.5) {
                            $realDay = 0.5;
                        }
                        $attendance[$key]['realDay'] = $realDay;
                        //caculate total minutes late
                        $attendance[$key]['total_minutes_late']=0;
                        if ($value['checkin'] != NULL){
                            $strStart = $date . " 08:30:00";
                            $attendance[$key]['total_minutes_late'] = minusDatetimeToHour($strStart, $value['checkin']);
                        }
                        $attendance[$key]['day_name'] = dayNameOfWeek($date);
                        //check explanation
                        $explanation = $this->explanation->getWhere(array('date_explanation'  =>  $date, 'id_user'  =>  $id, 'status'   =>  1));
                        $attendance[$key]['id_explanation'] = ($explanation != NULL) ? $explanation['id'] : NULL;
                        $attendance[$key]['date'] = $date;
                        $dataTimekeeping[$day] = $attendance[$key];
                    }
                    $key_day = date("d", strtotime($today));
                    unset($dataTimekeeping[$key_day]);
                    for ($i = 1; $i <= $total_days; $i++){
                        $day = $year . '-' . $month . '-' . (($i<10) ? '0'.$i : $i);
                        if (!isset($dataTimekeeping[$i])){
                            $explanation = $this->explanation->getWhere(array('date_explanation'    =>  $day, 'status'  =>  1, 'id_user'    =>  $id));
                            $dataTimekeeping[$i] = array(
                                'id'    =>  NULL,
                                'day_name'  =>  dayNameOfWeek($day),
                                'checkin'   =>  NULL,
                                'checkout'  =>  NULL,
                                'deduction'  =>  NULL,
                                'status'    => NULL,
                                'id_explanation'    =>  ($explanation) ? $explanation['id'] : NULL,
                                'id_user'   =>  $id,
                                'total_minutes' =>  NULL,
                                'realDay'   =>  NULL,
                                'total_minutes_late'    =>  NULL,
                                'date'  =>  $day
                            );
                        }
                    }
                }
            }
        }
        var_dump($dataTimekeeping);
        die();
        $this->render('page.timekeeping_manage', compact('dataTimekeeping', 'departments', 'teams', 'users'));
    }

    public function import(){
        if (!isAdmin()){
            redirect(base_url());
        }
        $exist = $this->session->flashdata('import_exist');
        $data = $this->session->flashdata('import_data');
        $this->render('page.import_timekeeping', compact('exist', 'data'));
    }

    public function postImport(){
        if (!isAdmin()){
            redirect(base_url());
        }
        $file = '';
        if (isset($_FILES['data']['tmp_name'])) $file = $_FILES['data']['tmp_name'];
        if ($this->input->post('file') !== NULL) $file = $this->input->post('file');
        if ($file !== ''){
            $data = array();
            $file = fopen($file, 'r');
            $option = $this->input->post('option');
            $dateImport = fgetcsv($file, 10000, ",");
            $dateQuery = $dateImport[1] . '-' . $dateImport[0];
            while (($row = fgetcsv($file, 10000, ",")) != FALSE)
            {
                $date = date('Y-m-d', strtotime($row[1]));
                $data[$row[0]][$date][] = array(
                    'id'    =>  $row[0],
                    'dateTime'    =>  $row[1],
                    'date'    =>  $date,
                    'time'    =>  date('H:i:s', strtotime($row[1])),
                );
            }
            fclose($file);
            if ( $option == 1 ){
                $allAtd = $this->timekeeping->getAll("(DATE_FORMAT(checkin,'%Y-%m') = '$dateQuery' OR DATE_FORMAT(checkout,'%Y-%m') = '$dateQuery' )");
                $arrAtd = array();
                foreach ($allAtd as $value){
                    $dateAtd = ($value['checkin'] !== NULL) ? date('Y-m-d', strtotime($value['checkin'])) : date('Y-m-d', strtotime($value['checkout']));
                    $id_user = $value['id_user'];
                    if (isset($data[$id_user][$dateAtd])){
                        unset($data[$id_user][$dateAtd]);
                    }
                }
            }
            else {
                if ($option == 3){
                    $this->timekeeping->deleteWhere("(DATE_FORMAT(checkin,'%Y-%m') = '$dateQuery' OR DATE_FORMAT(checkout,'%Y-%m') = '$dateQuery' )");
                }elseif($option == 2){
                    foreach ($data as $key => $value){
                        $where = '';
                        $where .= ' id_user = ' . $key ;
                        $dateWhere = '(';
                        foreach ($value as $key2 => $value2){
                            $dateWhere .=  '"' . $key2 . '"' . ((next($value)) ? ',' : '');
                        };
                        $dateWhere .= ')';
                        $where .= ' and ( IFNULL(DATE_FORMAT(checkin,"%Y-%m-%d"), DATE_FORMAT(checkout,"%Y-%m-%d")) in ' . $dateWhere. ' )';
                        $this->timekeeping->deleteWhere($where);
                    }
                }else{
                    $timekeeping = $this->timekeeping->count("(DATE_FORMAT(checkin,'%Y-%m') = '$dateQuery' OR DATE_FORMAT(checkout,'%Y-%m') = '$dateQuery' )");
                    if ($timekeeping > 0){
                        $config['upload_path'] = './public/upload_import/';
                        $config['allowed_types'] ='csv';
                        $config['max_size'] = '5000';
                        $config['file_name'] = 'upload' . time();
                        $this->load->library('upload', $config);
                        if(!$this->upload->do_upload('data')) {
                            echo $this->upload->display_errors();
                            die();
                        }
                        else {
                            $file_info = $this->upload->data();
                            $csvfilepath = "./public/upload_import/" . $file_info['file_name'];
                        }
                        $this->session->set_flashdata('import_exist', true);
                        $this->session->set_flashdata('import_data', $csvfilepath);
                        redirect(base_url('timekeeping/import'));
                    }
                }
            }
            $dataInsert = array();
            foreach ($data as $key => $value){
                $salary = $this->salary->getTotalSalaryOfUser($key);
                foreach ($value as $key2 => $value2){
                    $num = count($value2);
                    if($num == 1){
                        if(date('w', strtotime($value2[0]['date'])) == 6) {
                            if (strtotime($value2[0]['time']) > strtotime('11:00:00')){
                                $chechin = NULL;
                                $chechout = $value2[0]['dateTime'];
                            }else{
                                $chechin = $value2[0]['dateTime'];
                                $chechout = NULL;
                            }
                        }else{
                            $time = strtotime($value2[0]['time']);
                            if ($time < strtotime('10:00:00') || ($time > strtotime('13:00:00') && $time < strtotime('16:30:00'))){
                                $chechin = $value2[0]['dateTime'];
                                $chechout = NULL;
                            }else{
                                $chechout = $value2[0]['dateTime'];
                                $chechin = NULL;
                            }

                        }
                    }elseif ($num >1){
                        $chechin = $value2[0]['dateTime'];
                        $chechout = $value2[$num-1]['dateTime'];
                    }
                    $strEnd = $value2[0]['date'] . ' 08:30:00';
                    $minus_amount = 0;
                    if ($chechin != NULL){
                        $month = date('m', strtotime($key2));
                        $year = date('Y', strtotime($key2));
                        $workingDay = workingDay($month,$year);
                        $total_minutes = minusDatetimeToHour($strEnd, $chechin);
                        $deduction = $this->deduction->getWhere(array('start <'   =>  $total_minutes, 'end >='    =>  $total_minutes));
                        if(!$deduction){
                            $deduction = $this->deduction->getWhere(array('start <'   =>  $total_minutes),array('start', 'DESC'));
                        }
                        if($deduction){
                            if($deduction['unit'] == 0){
                                $minus_amount = $deduction['minus_amount'];
                            }else{
                                if (!$salary){
                                    $minus_amount = $total_minutes;
                                }else{
                                    $moneyofday = (float)($salary/$workingDay);
                                    $minus_amount = round($deduction['minus_amount']*$moneyofday, 0);
                                }
                            }
                        }
                    }
                    if ($chechin === NULL){
                        $status = 2;
                    }elseif($chechout === NULL){
                        $status = 0;
                    }else{
                        $status = 1;
                    }
                    $dataInsert[] = array(
                        'checkin'   =>  $chechin,
                        'checkout'   =>  $chechout,
                        'deduction'   =>  $minus_amount,
                        'status'    =>  $status,
                        'id_user'   =>  $key
                    );
                }
            }
            if($dataInsert){
                $this->db->insert_batch('timekeeping', $dataInsert);
            }
            $this->session->set_flashdata('notification', array('type'  =>  'success', 'title'  =>  'Nhập dữ liệu chấm công thành công', 'message'    =>  ''));
            redirect(base_url('timekeeping/import'));
        }
    }

    public function postImportUser(){
        if (isset($_FILES['data']['tmp_name'])){
            $file = fopen($_FILES['data']['tmp_name'], 'r');
            $data = array();
            while (($row = fgetcsv($file, 10000, ",")) != FALSE) //get row vales
            {
                //create email from fullname
                $str = trim(mb_strtolower($row[0]));
                $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
                $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
                $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
                $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
                $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
                $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
                $str = preg_replace('/(đ)/', 'd', $str);
                $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
                $str = preg_replace('/([\s]+)/', '', $str);
                //create data insert
                $data[] = array(
                    'id'    =>  $row[1],
                    'email' =>  $str . '@gmail.com',
                    'fullname' =>  $row[0],
                    'role'  =>  1,
                    'image'  =>  'photo.jpg',
                    'password'  =>  NULL,
                    'phone'  =>  NULL,
                    'gender'  =>  1,
                    'birthday'  =>  NULL,
                    'address'  =>  NULL,
                    'id_team'  =>  NULL,
                    'access_id'  =>  NULL
                );
            }
            fclose($file);
            $this->db->insert_batch('user', $data);
            $this->session->set_flashdata('notification', array('type'  =>  'success', 'title'  =>  'Nhập dữ liệu thành công', 'message'    =>  ''));
            redirect(base_url('timekeeping/import'));
        }
    }

    public function postImportSalary(){
        if (isset($_FILES['data']['tmp_name'])){
            $file = fopen($_FILES['data']['tmp_name'], 'r');
            $data = array();
            while (($row = fgetcsv($file, 10000, ",")) != FALSE) //get row vales
            {
                //create data insert
                $hard_salary = rand(5,20) . '000000';
                $subsidize = rand(1,9) . '00000';
                $data[] = array(
                    'id_user'    =>  $row[1],
                    'hard_salary'   =>      $hard_salary,
                    'subsidize' =>  $subsidize
                );
            }
            fclose($file);
            $this->db->insert_batch('salary', $data);
            $this->session->set_flashdata('notification', array('type'  =>  'success', 'title'  =>  'Nhập dữ liệu lương thành công', 'message'    =>  ''));
            redirect(base_url('timekeeping/import'));
        }
    }

}
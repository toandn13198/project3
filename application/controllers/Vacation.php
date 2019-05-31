<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vacation extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('vacation_model', 'vacation');
        $this->load->model('timekeeping_model', 'timekeeping');
        $this->load->model('department_model', 'department');
        $this->load->model('team_model', 'team');
        $this->load->model('user_model', 'user');
    }

    public function approveRequest(){
        if (!(isAdmin() || isLeader()))
            redirect(base_url());
        $where = ($this->input->get('keyword') != null) ? array('fullname like' => '%' . $this->input->get('keyword') . '%') : array();
        $config['params'] = ($this->input->get('keyword') != null) ? array('keyword'    =>  $this->input->get('keyword')) : array();
        if($this->input->get('datefilter')){
            $arrDate = explode(' - ', $this->input->get('datefilter'));
            $date_from = date('Y-m-d',strtotime(str_replace('/', '-', $arrDate[0])));
            $date_to = date('Y-m-d',strtotime(str_replace('/', '-', $arrDate[1])));
            $where['vacation.date_submit >='] = $date_from;
            $where['vacation.date_submit <='] = $date_to;
            $config['params']['datefilter'] = $this->input->get('datefilter');
        }
        $where['vacation.status'] = 0;
        $where['vacation.approver'] = $this->session->id;
        $limit = 6;
        $config['total'] = $this->vacation->countRequestVacation($where);
        $config['limit'] = $limit;
        $config['page_current'] = ($this->input->get('page') != null) ? $this->input->get('page') : 1;
        $config['url'] = base_url('vacation/approveRequest/');
        $this->load->library('paginator',$config);
        $pagi = $this->paginator;
        $page = $this->paginator->getPage();
        $start = ($page-1)*$limit;
        $requestVacation = $this->vacation->getRequestVacation(array($start,$limit),$where);
        $this->render('page.vacation_list', compact('requestVacation', 'pagi'));
    }

    public function approved(){
        if (!(isAdmin() || isLeader()))
            redirect(base_url());
        $where = ($this->input->get('keyword') != null) ? array('fullname like' => '%' . $this->input->get('keyword') . '%') : array();
        $config['params'] = ($this->input->get('keyword') != null) ? array('keyword'    =>  $this->input->get('keyword')) : array();
        if($this->input->get('datefilter')){
            $arrDate = explode(' - ', $this->input->get('datefilter'));
            $date_from = date('Y-m-d',strtotime(str_replace('/', '-', $arrDate[0])));
            $date_to = date('Y-m-d',strtotime(str_replace('/', '-', $arrDate[1])));
            $where['vacation.date_submit >='] = $date_from;
            $where['vacation.date_submit <='] = $date_to;
            $config['params']['datefilter'] = $this->input->get('datefilter');
        }
        $status = ['1','2'];
        if($this->input->get('status') != null && in_array($this->input->get('status'), $status)) {
            $where['vacation.status'] = $this->input->get('status');
            $config['params']['status'] = $this->input->get('status');
        }else{
            $where['vacation.status <>'] = 0;
        }
        $where['vacation.approver'] = $this->session->id;
        $limit = 6;
        $config['total'] = $this->vacation->countRequestVacation($where);
        $config['limit'] = $limit;
        $config['page_current'] = ($this->input->get('page') != null) ? $this->input->get('page') : 1;
        $config['url'] = base_url('vacation/approved/');
        $this->load->library('paginator',$config);
        $pagi = $this->paginator;
        $page = $this->paginator->getPage();
        $start = ($page-1)*$limit;
        $listVacation = $this->vacation->getRequestVacation(array($start,$limit),$where);
        $this->render('page.vacation_approved', compact('listVacation', 'pagi'));
    }

    public function add(){
        $approvers = array();
        if((isLeader() || $this->session->id_team == NULL || !haveLeader()) && !isAdmin()){
            $approvers = $this->user->getAll(array('role'    =>  3));
        }
        $members = $this->team->getUsersOfTeam($this->session->id_team);
        foreach ($members as $key => $member){
            if ($member['id'] == $this->session->id){
                unset($members[$key]);
            }
        }
        if(!$members)
            $members = $this->user->getAll(array('id !=' => $this->session->id));
        $this->render('page.vacation_form', compact('members', 'approvers'));
    }

    public function insert(){
        $data = array(
            'reason'    =>  $this->input->post('reason'),
            'date_submit'    =>  $this->vacation->getDateTime(),
            'date_from'    =>   date('Y-m-d H:i:s',strtotime($this->input->post('date_from'))),
            'date_to'    =>  date('Y-m-d H:i:s',strtotime($this->input->post('date_to'))),
            'handover_id'    =>  $this->input->post('handover_id'),
            'handover_work'    =>  $this->input->post('handover_work'),
            'status'    =>  0,
            'id_user'   =>  $this->session->id
        );
        $dayVacation = 0;
        $date_from = new DateTime($data['date_from']);
        $date_to  = new DateTime($data['date_to']);
        $dteDiff = $date_from->diff($date_to);
        $dateTime = $dteDiff->format("%D %h:%i");
        $dateTime = explode(" ", $dateTime);
        $dayVacation += (int)$dateTime[0];
        $time = explode(':', $dateTime[1]);
        $minutes = $time[0]*60 + $time[1];
        $dayVacation += realWorkingDay($minutes);
        $data['day_number'] = $dayVacation;
        if((isLeader() || $this->session->id_team == NULL || !haveLeader()) && !isAdmin()){
            $data['approver'] = $this->input->post('approver');
        }else if(isAdmin()) {
            $data['approver'] = $this->session->id;
        }else if(haveLeader()){
            $data['approver'] = haveLeader();
        }
        if (isAdmin() && ($this->session->id_team == NULL || !haveLeader()))
            $data['status'] = 1;
        $this->vacation->insert($data);
        redirect(base_url('vacation/listSent'));
    }

    public function getById($id){
        $data = $this->vacation->getVacation($id);
        if (($this->session->id != $data['id_user']) && !isAdmin() && !isLeader())
            redirect(base_url());
        //approver
        $data['approver_info'] = $this->user->getWhere(array('id'  =>  $data['approver']));
        //
        $data['date_from'] = date('H:i d-m-Y',strtotime($data['date_from']));
        $data['date_to'] = date('H:i d-m-Y',strtotime($data['date_to']));
        $data['date_submit'] = date('d-m-Y',strtotime($data['date_submit']));
        echo json_encode($data);
    }


    public function listSent(){
        $approvers = array();
        if((isLeader() || $this->session->id_team == NULL || !haveLeader()) && !isAdmin()){
            $approvers = $this->user->getAll(array('role'    =>  3));
        }
        //
        $members = $this->team->getUsersOfTeam($this->session->id_team);
        foreach ($members as $key => $member){
            if ($member['id'] == $this->session->id){
                unset($members[$key]);
            }
        }
        if(!$members)
            $members = $this->user->getAll(array('id !=' => $this->session->id));
        //paginate

        $status = ['0','1','2'];
        $where = ($this->input->get('status') != null && in_array($this->input->get('status'), $status)) ? array('status' => $this->input->get('status'),'id_user' => $this->session->id) : array('id_user' => $this->session->id);
        $config['params'] = ($this->input->get('status') != null && in_array($this->input->get('status'), $status)) ? array('status'    =>  $this->input->get('status')) : array();
        if($this->input->get('datefilter')){
            $arrDate = explode(' - ', $this->input->get('datefilter'));
            $date_from = date('Y-m-d',strtotime(str_replace('/', '-', $arrDate[0])));
            $date_to = date('Y-m-d',strtotime(str_replace('/', '-', $arrDate[1])));
            $where['vacation.date_submit >='] = $date_from;
            $where['vacation.date_submit <='] = $date_to;
            $config['params']['datefilter'] = $this->input->get('datefilter');
        }
        $limit = 6;
        $config['total'] = $this->vacation->count($where);
        $config['limit'] = $limit;
        $config['page_current'] = ($this->input->get('page') != null) ? $this->input->get('page') : 1;
        $config['url'] = base_url('vacation/listSent');
        $this->load->library('paginator',$config);
        $pagi = $this->paginator;
        $page = $this->paginator->getPage();
        $start = ($page-1)*$limit;
        $listVacation = $this->vacation->getAll($where, array('id', 'DESC'), array($start,$limit));
        $this->render('page.vacation_sent', compact('listVacation', 'members', 'pagi', 'approvers'));
    }

    public function update(){
        $vacation = $this->vacation->getWhere(array('id'    =>  $this->input->post('id')));
        if ($vacation['id_user'] != $this->session->id){
            redirect(base_url());
        }
        $data = array(
            'reason'    =>  $this->input->post('reason'),
            'date_from'    =>   date('Y-m-d H:i:s',strtotime($this->input->post('date_from'))),
            'date_to'    =>  date('Y-m-d H:i:s',strtotime($this->input->post('date_to'))),
            'handover_work'    =>  $this->input->post('handover_work')
        );
        $dayVacation = 0;
        $date_from = new DateTime($data['date_from']);
        $date_to  = new DateTime($data['date_to']);
        $dteDiff = $date_from->diff($date_to);
        $dateTime = $dteDiff->format("%D %h:%i");
        $dateTime = explode(" ", $dateTime);
        $dayVacation += (int)$dateTime[0];
        $time = explode(':', $dateTime[1]);
        $minutes = $time[0]*60 + $time[1];
        $dayVacation += realWorkingDay($minutes);
        $data['day_number'] = $dayVacation;
        if ($this->input->post('handover_id')){
            $data['handover_id'] =  $this->input->post('handover_id');
        }
        if ($this->input->post('approver')){
            $data['approver'] =  $this->input->post('approver');
        }
        $this->vacation->update($data, $this->input->post('id'));
        redirect(base_url('vacation/listSent'));
    }

    public function delete($id){
        $vacation = $this->vacation->getWhere(array('id'    =>  $id));
        if ($vacation['id_user'] != $this->session->id){
            redirect(base_url());
        }
        $this->vacation->delete($id);
        redirect(base_url('vacation/listSent'));
    }

    public function confirm($id){
        $vacation = $this->vacation->getWhere(array('id'    =>  $id));
        if ($vacation['approver'] != $this->session->id){
            redirect(base_url());
        }
        $this->vacation->update(array('status'  =>  '1'), $id);
        redirect(base_url('vacation/approveRequest'));
    }

    public function reject($id){
        $vacation = $this->vacation->getWhere(array('id'    =>  $id));
        if ($vacation['approver'] != $this->session->id){
            redirect(base_url());
        }
        $this->vacation->update(array('status'  =>  '2'), $id);
        redirect(base_url('vacation/approveRequest'));
    }

    public function getVacationById($id = NULL){
        if ($id === NULL){
            $id = $this->session->id;
        }
        $data = array();
        $allowed = 0 ;
        $unauthorized = 0;
        if ($this->input->get('month')){
            $now = $this->input->get('month');
            $now = explode('-', $now);
            $now = $now[1] . '-' . $now[0];
            $today = $this->vacation->getDateTime();
            $monthYear = date('Y-m', strtotime($today));
            if ($now <= $monthYear){
                $arrNow = explode("-", $now);
                $month = $arrNow[1];
                $year = $arrNow[0];
                if($now == $monthYear) {
                    $total_days = (int)date('d', strtotime($today))-1;
                }else{
                    $total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                }
                for ($i=1; $i <= $total_days; $i++) {
                    $day = $year . '-' . $month . '-' . (($i<10) ? '0'.$i : $i);
                    $dayOfWeek  = date('w', strtotime($day));
                    $where = "id_user = $id AND (DATE_FORMAT(checkin,'%Y-%m-%d') = '$day' OR DATE_FORMAT(checkout,'%Y-%m-%d') = '$day' )";
                    if ($dayOfWeek != '0') {
                        $attendance = $this->timekeeping->getAttendanceHistory($where);
                        $attendance = ($attendance) ? $attendance[0] : false;
                        if ($attendance) {
                            $lastAttendance = $this->timekeeping->getWhere("id_user = " . $id . " AND  DATE_FORMAT(checkin,'%Y-%m-%d') = DATE_FORMAT('" . $attendance['checkin'] . "','%Y-%m-%d') and checkout is null", array('checkin', 'DESC'));
                            //missing checkout
                            if ($lastAttendance) {
                                if(date('w', strtotime($day)) == 6){
                                    $checkout = $day . " 12:00:59";
                                }else{
                                    $checkout = $day . " 18:00:59";
                                }
                            }else{
                                $checkout = $attendance['checkout'] ;
                            }
                            //missing checkin
                            if ($attendance['checkin'] === NULL){
                                $checkin = $day . ' 08:30:00';
                            }else{
                                $checkin = $attendance['checkin'];
                            }
                            $checkin = date('H:i:s', strtotime($checkin));
                            $checkout = date('H:i:s', strtotime($checkout));
                            if($dayOfWeek == '6') {
                                if (strtotime($checkin) >= strtotime('12:00:00')){
                                    $data[] = array(
                                        'date' => $day,
                                        'time'  => 1,
                                        'number' => 0.5
                                    );
                                }
                            }else{
                                if (strtotime($checkin) >= strtotime('12:00:00')){
                                    //nghi buoi sang
                                    $data[] = array(
                                        'date' => $day,
                                        'time'  => 1,
                                        'number' => 0.5
                                    );
                                }elseif(strtotime($checkout) <= strtotime('13:30:00')){
                                    //nghi buoi chieu
                                    $data[] = array(
                                        'date' => $day,
                                        'time'  =>  2,
                                        'number' => 0.5
                                    );
                                }
                            }

                        } else {
                            $data[] = array(
                                'date' => $day,
                                'time'  => ($dayOfWeek == '6') ? 1 : NULL,
                                'number' => ($dayOfWeek == '6') ? 0.5 : 1
                            );
                        }
                    }

                }

                foreach ($data as $key => $value){
                    $where = array(
                        "DATE_FORMAT(date_from,'%Y-%m-%d') <="  =>  $value['date'],
                        "DATE_FORMAT(date_to,'%Y-%m-%d') >="  =>  $value['date'],
                        "id_user"   =>  $id,
                        "status"    =>  1
                    );
                    $vation = $this->vacation->getWhere($where);
                    if ($vation){
                        $from  = new DateTime($vation['date_from']);
                        $to  = new DateTime($vation['date_to']);
                        $condi1 = new DateTime($value['date'] . ' 12:00:00' );
                        $condi2 = new DateTime($value['date'] . ' 13:30:00' );
                        if ($value['number'] == 0.5){
                            if ($value['time']==1){
                                if ($from >= $condi1 ){
                                    $data[$key]['status'] = 0;
                                    $unauthorized+=0.5;
                                }else{
                                    $data[$key]['status'] = 1;
                                    $allowed+=0.5;
                                }
                            }elseif ($value['time']==2){
                                if ($to <= $condi2 ){
                                    $data[$key]['status'] = 0;
                                    $unauthorized+=0.5;
                                }else{
                                    $data[$key]['status'] = 1;
                                    $allowed+=0.5;
                                }
                            }
                        }else{
                            $data[$key]['status'] = NULL;
                            if ($from >= $condi1){
                                $data[$key]['morning'] = 0;
                                $data[$key]['afternoon'] = 1;
                                $allowed+=0.5;
                                $unauthorized+=0.5;
                            }elseif($to <= $condi2 ){
                                $data[$key]['morning'] = 1;
                                $data[$key]['afternoon'] = 0;
                                $allowed+=0.5;
                                $unauthorized+=0.5;
                            }else{
                                $data[$key]['status'] = 1;
                                $allowed+=$value['number'];
                            }
                        }
                    }else{
                        $data[$key]['status'] = 0;
                        $unauthorized+=$value['number'];
                    }
                }
            }
        }
        return array(
            'data'  => $data,
            'allowed'   =>  $allowed,
            'unauthorized'  =>  $unauthorized
        );
    }

    public function vacationHistory(){
        $dataVacation = $this->getVacationById();
        $this->render('page.vacation_history', $dataVacation);
    }

    public function statistics_vacation(){
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
            $dataVacation = $this->user->getWhere(array('id'    => $id ));
            $dataVacation['data']   =   $this->getVacationById($id);
        }else{
            $dataVacation = array();
        }
        $this->render('page.vacation_statistics', compact('dataVacation', 'departments', 'teams', 'users'));
    }

}
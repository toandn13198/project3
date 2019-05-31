<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends MY_Controller
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

    public function dashboard(){
        if (!(isAdmin() || isHR()))
            redirect(base_url('user/dashboard_user'));
        $now = $this->team->getDateTime();
        $date = date('Y-m-d', strtotime($now));
        $department = $this->department->count();
        $team = $this->team->count();
        $user = $this->user->count();
        $where = array(
            "DATE_FORMAT(date_from,'%Y-%m-%d') <=" => $date,
            "DATE_FORMAT(date_to,'%Y-%m-%d') >=" => $date,
            "status"    =>  1
        );
        $this->db->select('DISTINCT `id_user`', FALSE);
        $this->db->where($where);
        $query = $this->db->get('vacation');
        $vacation = $query->num_rows();
        $this->render('page.dashboard', compact('department', 'team', 'user', 'vacation'));
    }
}
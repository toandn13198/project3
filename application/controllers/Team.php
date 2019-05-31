<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Team extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('team_model', 'team');
        $this->load->model('user_model', 'user');
        $this->load->model('department_model', 'department');
    }

    public function index(){
        if(!(isAdmin() || isHR()))
            redirect(base_url('user/dashboard_user'));
        $config['params'] = array();
        $where = array();
        $departments = $this->department->getAll();
        $idDepartment = convertToArrayId($departments);
        if(in_array($this->input->get('department'), $idDepartment)){
            $where['team.id_department'] = $this->input->get('department');
            $config['params']['department'] = $this->input->get('department');
        }
        if($this->input->get('keyword') != null) {
            $where['team.name like'] = '%' . $this->input->get('keyword') . '%';
            $config['params']['keyword'] = $this->input->get('keyword');
        }
        $limit = 6;
        $config['total'] = $this->team->count($where);
        $config['limit'] = $limit;
        $config['page_current'] = ($this->input->get('page') != null) ? $this->input->get('page') : 1;
        $config['url'] = base_url('team/index/');
        $this->load->library('paginator',$config);
        $pagi = $this->paginator;
        $page = $this->paginator->getPage();
        $start = ($page-1)*$limit;
        $data = $this->team->getAllTeam(array($start,$limit),$where);
        $this->render('page.team_list', compact('data', 'departments', 'pagi'));
    }

    public function add(){
        if(!(isAdmin() || isHR()))
            redirect(base_url('user/dashboard_user'));
        $departments = $this->department->getAll();
        $users = $this->team->getUsersNoTeam();
        $this->render('page.team_add',compact('departments', 'users'));
    }

    public function insert(){
        if(!(isAdmin() || isHR()))
            redirect(base_url('user/dashboard_user'));
        $data = array(
            'name'  => $this->input->post('name'),
            'leader'  => $this->input->post('leader'),
            'id_department'  => $this->input->post('department')
        );
        $id_team = $this->team->insert($data);
        $members = ($this->input->post('member') != null) ? $this->input->post('member') : array();
        if (!in_array($data['leader'],$members) && $data['leader'] != NULL){
            array_push($members, $data['leader']);
        }
        $this->team->setMember($members, $id_team);
        kickUser($members);
        redirect(base_url('team'));
    }

    public function edit($id){
        if(!(isAdmin() || isHR()))
            redirect(base_url('user/dashboard_user'));
        $departments = $this->department->getAll();
        $team = $this->team->getWhere(array('id'  =>  $id));
        $usersOnTeam = $this->team->getUsersCanOnTeam($team['id']);
        $usersOfTeam = $this->team->getUsersOfTeam($team['id']);
        $this->render('page.team_edit', compact('team', 'departments', 'usersOnTeam', 'usersOfTeam'));
    }

    public function update(){
        if(!(isAdmin() || isHR()))
            redirect(base_url('user/dashboard_user'));
        //receive data
        $data = array(
            'name'  => $this->input->post('name'),
            'leader'  => $this->input->post('leader'),
            'id_department'  => $this->input->post('department')
        );
        //update basic info of team
        $this->team->update($data, $this->input->post('id'));

        $userOfTeam = $this->team->getUsersOfTeam($this->input->post('id'));
        $userOfTeam = convertToArrayId($userOfTeam);
        $members = ($this->input->post('member') != null) ? $this->input->post('member') : array();
        //add member to the team
        $listIdUpdate = array_diff($members,$userOfTeam);
        if (!in_array($data['leader'],$listIdUpdate) && $data['leader'] != NULL){
            array_push($listIdUpdate, $data['leader']);
        }
        $this->team->setMember($listIdUpdate, $this->input->post('id'));
        //delete member from team
        $listIdRemove = array_diff($userOfTeam,$members);
        $this->team->unsetMember($listIdRemove);
        $list = array_merge($userOfTeam, $listIdUpdate);
        kickUser($list);
        redirect(base_url('team'));
    }

    public function delete($id){
        if(!(isAdmin() || isHR()))
            redirect(base_url('user/dashboard_user'));
        $userOfTeam = $this->team->getUsersOfTeam($id);
        $userOfTeam = convertToArrayId($userOfTeam);
        $this->team->unsetMember($userOfTeam);
        $this->team->delete($id);
        kickUser($userOfTeam);
        redirect(base_url('team'));
    }

    public function detail($id = 0){
        if(!(isAdmin() || isHR()))
            redirect(base_url('user/dashboard_user'));
        $team = $this->team->getWhere(array('id'    =>  $id));
        $otherTeam = $this->team->getAll(array('id <>'    =>  $id));
        $members = $this->team->getUsersOfTeam($id);
        $users = $this->team->getUsersNoTeam();
        $this->render('page.team_detail', compact('team', 'members', 'users', 'otherTeam'));
    }

    public function detailMyTeam(){
        $team = $this->team->getWhere(array('id'    =>  $this->session->id_team));
        $otherTeam = $this->team->getAll(array('id <>'    =>  $this->session->id_team));
        $members = $this->team->getUsersOfTeam($this->session->id_team);
        $users = $this->team->getUsersNoTeam();
        $this->render('page.team_detail', compact('team', 'members', 'users', 'otherTeam'));
    }

    public function setLeader($id_team,$id_leader){
        if(!(isAdmin() || isHR()))
            redirect(base_url('user/dashboard_user'));
        $this->team->update(array('leader'  => $id_leader), $id_team);
        $userOfTeam = $this->team->getUsersOfTeam($id_team);
        $userOfTeam = convertToArrayId($userOfTeam);
        kickUser($userOfTeam);
        redirect(base_url('team/detail/' . $id_team));
    }

    public function unsetLeader($id_team){
        if(!(isAdmin() || isHR()))
            redirect(base_url('user/dashboard_user'));
        $this->team->update(array('leader'  =>  NULL), $id_team);
        $userOfTeam = $this->team->getUsersOfTeam($id_team);
        $userOfTeam = convertToArrayId($userOfTeam);
        kickUser($userOfTeam);
        redirect(base_url('team/detail/' . $id_team));
    }

    public function removeMember($id_team, $id_member){
        if(!(isAdmin() || isHR() || (isLeader() && $id_team == $this->session->id_team && $id_member != $this->session->id)))
            redirect(base_url('user/dashboard_user'));
        if($this->team->isLeader($id_team, $id_member)){
            $this->team->update(array('leader'  =>  NULL), $id_team);
        };
        $this->user->update(array('id_team' =>  NULL), $id_member);
        kickUser(array($id_member));
        redirect(base_url('team/detail/' . $id_team));
    }

    public function addMember($id_team){
        if(!(isAdmin() || isHR() || (isLeader() && $id_team == $this->session->id_team)))
            redirect(base_url('user/dashboard_user'));
        $members = ($this->input->post('member') != null) ? $this->input->post('member') : array();
        $this->team->setMember($members, $id_team );
        kickUser($members);
        redirect(base_url('team/detail/' . $id_team));
    }

    public function checkName(){
        if(!(isAdmin() || isHR()))
            redirect(base_url('user/dashboard_user'));
        $name = trim($this->input->get('name'));
        $where = array('name'   => $name);
        if ($this->input->get('id')){
            $where['id <>'] = $this->input->get('id');
        }
        if($this->team->checkUnique($where)){
            echo 'true';
        }else{
            echo 'false';
        };
    }

    public function moveTeam(){
        if(!(isAdmin() || isHR()))
            redirect(base_url('user/dashboard_user'));
        $id = $this->input->post('id');
        $old_team = $this->input->post('old_team');
        $team = $this->input->post('team');
        $role = $this->input->post('role');
        if($this->team->isLeader($old_team, $id)){
            $this->team->update(array('leader'  =>  NULL), $old_team);
            $mem = $this->team->getUsersOfTeam($old_team);
            $mem = convertToArrayId($mem);
            kickUser($mem);
        }
        if ($role == 2){
            $this->team->update(array('leader'  =>  $id), $team);
            $mem = $this->team->getUsersOfTeam($team);
            $mem = convertToArrayId($mem);
            kickUser($mem);
        }
        $this->user->update(array('id_team' =>  $team), $id);
        kickUser(array($id));
        redirect(base_url('team/detail/' . $team));
    }

}
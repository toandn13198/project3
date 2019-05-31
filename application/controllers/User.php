<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model','user');
        $this->load->model('department_model','department');
        $this->load->model('team_model','team');
    }

    public function index(){
        if (!(isHR() || isAdmin()))
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
            if ($this->input->get('employee-type') == 1){
                $where['user.id_team <>'] = NULL;
            }
        }
        if($this->input->get('keyword') != null) {
            $where['user.fullname like'] = '%' . $this->input->get('keyword') . '%';
            $config['params']['keyword'] = $this->input->get('keyword');
        }
        $limit = 6;
        $config['total'] = $this->user->countUser($where);
        $config['limit'] = $limit;
        $config['page_current'] = ($this->input->get('page') != null) ? $this->input->get('page') : 1;
        $config['url'] = base_url('user/index/');
        $this->load->library('paginator',$config);
        $pagi = $this->paginator;
        $page = $this->paginator->getPage();
        $start = ($page-1)*$limit;
        $data = $this->user->getAllUser(array($start,$limit),$where);
        $this->render('page.user_list', compact('data', 'pagi', 'departments', 'teams'));
    }

    public function add(){
        if (!(isHR() || isAdmin()))
            redirect(base_url('user/dashboard_user'));
        $teams = $this->team->getAll();
        $this->render('page.user_add', compact('teams'));
    }

    public function insert(){
        if (!(isHR() || isAdmin()))
            redirect(base_url('user/dashboard_user'));
        $data = array(
            'email'  => $this->input->post('email'),
            'phone'  => $this->input->post('phone'),
            'fullname'  => $this->input->post('fullname'),
            'gender'  => $this->input->post('gender'),
            'birthday'  => date('Y-m-d',strtotime($this->input->post('birthday'))),
            'address'  => $this->input->post('address'),
            'role'  => $this->input->post('role'),
            'id_team'  => $this->input->post('team')
        );
        if(($this->input->post('role') == 2  && isHR()) || $this->input->post('role') == 3)
            redirect(base_url());
        $data['image'] = ($data['gender'] == 0) ? '4.jpg' : '5.jpg';
        $this->user->insert($data);
        redirect(base_url('user'));
    }

    public function edit($id){
        if (!(isHR() || isAdmin()))
            redirect(base_url('user/dashboard_user'));
        $teams = $this->team->getAll();
        $user = $this->user->getWhere(array('id'    =>  $id));
        $this->render('page.user_edit', compact('user', 'teams'));
    }

    public function update(){
        if (!(isHR() || isAdmin()))
            redirect(base_url('user/dashboard_user'));
        $data = array(
            'email'  => $this->input->post('email'),
            'phone'  => $this->input->post('phone'),
            'fullname'  => $this->input->post('fullname'),
            'gender'  => $this->input->post('gender'),
            'birthday'  => date('Y-m-d',strtotime($this->input->post('birthday'))),
            'address'  => $this->input->post('address'),
            'role'  => $this->input->post('role'),
            'id_team'  => $this->input->post('team')
        );
        if(($this->input->post('role') == 2  && isHR()) || $this->input->post('role') == 3)
            redirect(base_url());
        $this->user->update($data, $this->input->post('id'));
        kickUser(array($this->input->post('id')));
        redirect(base_url('user'));
    }

    public function delete($id){
        if (!(isHR() || isAdmin()))
            redirect(base_url('user/dashboard_user'));
        $user = $this->user->getWhere(array('id'    => $id));
        if($this->session->role <= $user['role'])
            redirect(base_url());
        kickUser($id);
        $this->user->delete($id);
        redirect(base_url('user'));
    }

    public function logout(){
        $this->session->sess_destroy();;
        redirect(base_url('login'));
    }

    public function changeInfo(){
        $user = $this->user->getWhere(array('id'    =>  $this->session->id));
        $this->render('page.change_info', compact('user'));
    }

    public function postChangeInfo(){
        $data = array(
            'phone'  => $this->input->post('phone'),
            'fullname'  => $this->input->post('fullname'),
            'gender'  => $this->input->post('gender'),
            'birthday'  => date('Y-m-d',strtotime($this->input->post('birthday'))),
            'address'  => $this->input->post('address')
        );
        if (!empty($_FILES['image']['name'])) {
            $config['upload_path'] = 'public/elaAdmin/images/users/';
            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size'] = 1024 * 5;
            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('image')) {
                $error = array('error' => $this->upload->display_errors());
            } else {
                $uploadData = $this->upload->data();
                $data["image"] = $uploadData['file_name'];
                $old_image_name = $this->input->post('old-image');
                if ($old_image_name != '4.jpg' && $old_image_name != '5.jpg' && $old_image_name != 'photo.jpg') {
                    $old_image = $config['upload_path'] . $old_image_name;
                    if (file_exists($old_image)) {
                        unlink($old_image);
                    }
                }
            }
        }
        $this->user->update($data, $this->session->id);
        $this->session->set_userdata($data);
        redirect(base_url('user/changeInfo'));
    }

    public function changePass(){
        $error_changepass = $this->session->flashdata('error_changepass');
        $this->render('page.change_password', compact('error_changepass'));
    }

    public function postChangePass(){
        $where = array(
            'id' =>  $this->session->id,
            'password'  =>  md5($this->input->post('currentPassword'))
        );
        $user = $this->user->count($where);
        if($user === 1){
            $data = array('password'    =>  md5($this->input->post('newPassword')));
            $this->user->update($data, $this->session->id);
            redirect(base_url());
        }else{
            $this->session->set_flashdata('error_changepass', 'Mật khẩu hiện tại không chính xác!');
            redirect(base_url('user/changepass'));
        }
    }

    public function dashboard_user(){
        $this->render('page.dashboard_user');
    }

    public function checkEmail(){
        $email = trim($this->input->get('email'));
        $where = array('email'   => $email);
        if ($this->input->get('id')){
            $where['id <>'] = $this->input->get('id');
        }
        if($this->user->checkUnique($where)){
            echo 'true';
        }else{
            echo 'false';
        };
    }

    public function checkPassword(){
        $condition = array(
            'password'  =>  md5($this->input->get('currentPassword')),
            'id'  =>  $this->session->id
        );
        if(!$this->user->checkUnique($condition)){
            echo 'true';
        }else{
            echo 'false';
        };
    }

}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Department extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('department_model','department');
        if(!(isAdmin() || isHR()))
            redirect(base_url('user/dashboard_user'));
    }

    public function index(){
        $config['params'] = array();
        $where = array();
        if($this->input->get('keyword') != null) {
            $where['name like'] = '%' . $this->input->get('keyword') . '%';
            $config['params']['keyword'] = $this->input->get('keyword');
        }
        $limit = 6;
        $config['total'] = $this->department->count($where);
        $config['limit'] = $limit;
        $config['page_current'] = ($this->input->get('page') != null) ? $this->input->get('page') : 1;
        $config['url'] = base_url('department/index/');
        $this->load->library('paginator',$config);
        $pagi = $this->paginator;
        $page = $this->paginator->getPage();
        $start = ($page-1)*$limit;
        $data = $this->department->getAll($where, array('id','desc'), array($start,$limit));
        $this->render('page.department_list',compact('data', 'pagi'));
    }

    public function add(){
        $this->render('page.department_add');
    }

    public function insert(){
        $data = array(
            'name'  => $this->input->post('name'),
            'description'  => $this->input->post('description')
        );
        $this->department->insert($data);
        redirect(base_url('department'));
    }

    public function edit($id){
        $department = $this->department->getWhere(array('id'    =>  $id));
        $this->render('page.department_edit',$department);
    }

    public function update(){
        $data = array(
            'name'  =>  $this->input->post('name'),
            'description'  =>  $this->input->post('description')
        );
        $this->department->update($data, $this->input->post('id'));
        redirect(base_url('department'));
    }

    public function delete($id){
        $this->department->delete($id);
        redirect(base_url('department'));
    }

    public function checkName(){
        $name = trim($this->input->get('name'));
        $where = array('name'   => $name);
        if ($this->input->get('id')){
            $where['id <>'] = $this->input->get('id');
        }
        if($this->department->checkUnique($where)){
            echo 'true';
        }else{
            echo 'false';
        };
    }

}
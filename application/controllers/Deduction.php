<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Deduction extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('deduction_model', 'deduction');
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
        $config['total'] = $this->deduction->count($where);
        $config['limit'] = $limit;
        $config['page_current'] = ($this->input->get('page') != null) ? $this->input->get('page') : 1;
        $config['url'] = base_url('deduction/index/');
        $this->load->library('paginator',$config);
        $pagi = $this->paginator;
        $page = $this->paginator->getPage();
        $start = ($page-1)*$limit;
        $deductions = $this->deduction->getAll($where, array('id', 'DESC'), array($start,$limit));
        $this->render('page.deduction_list',compact('deductions', 'pagi'));
    }

    public function add(){
        $this->render('page.deduction_add');
    }

    public function insert(){
        $data = array(
            'name'  =>  $this->input->post('name'),
            'start'  =>  $this->input->post('start'),
            'end'  =>  ($this->input->post('end')) ? $this->input->post('end') : NULL,
            'minus_amount'  =>  $this->input->post('minus_amount'),
            'unit'  =>  $this->input->post('unit')
        );
        $this->deduction->insert($data);
        redirect(base_url('deduction'));
    }

    public function edit($id){
        $deduction = $this->deduction->getWhere(array('id'  => $id));
        $this->render('page.deduction_edit', $deduction);
    }

    public function update(){
        $data = array(
            'name'  =>  $this->input->post('name'),
            'start'  =>  $this->input->post('start'),
            'end'  =>  ($this->input->post('end')) ? $this->input->post('end') : NULL,
            'minus_amount'  =>  $this->input->post('minus_amount'),
            'unit'  =>  $this->input->post('unit')
        );
        $this->deduction->update($data, $this->input->post('id'));
        redirect(base_url('deduction'));
    }

    public function delete($id){
        $this->deduction->delete($id);
        redirect(base_url('deduction'));
    }

}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Explanation extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('explanation_model', 'explanation');
        $this->load->model('timekeeping_model', 'timekeeping');
        $this->load->model('team_model', 'team');
    }

    public function add(){
        $data = array(
            'date_explanation'  =>  $this->input->post('date_explanation'),
            'content'   =>  $this->input->post('content'),
            'status'    =>  0,
            'date_submit'   =>  $this->explanation->getDateTime(),
            'id_user'   =>  $this->session->id
        );
        if((isLeader() || $this->session->id_team == NULL || !haveLeader()) && !isAdmin()){
            $data['approver'] = $this->input->post('approver');
        }else if(isAdmin()) {
            $data['approver'] = $this->session->id;
        }else if(haveLeader()){
            $data['approver'] = haveLeader();
        }
        if (isAdmin() && ($this->session->id_team == NULL || !haveLeader() || isLeader()))
            $data['status'] = 1;
        $insert = $this->explanation->insert($data);
        redirectPreUrl(base_url('timekeeping/history'));
    }

    public function getById($id){
        $data = $this->explanation->getExplanation($id);
        echo json_encode($data);
    }

    public function update(){
        $data = array(
            'content'   =>  $this->input->post('content'),
        );
        if($this->input->post('approver')){
            $data['approver'] = $this->input->post('approver');
        }
        $this->explanation->update($data, $this->input->post('id'));
        redirectPreUrl(base_url('timekeeping/history'));
    }

    public function delete($id){
        $this->explanation->delete($id);
        $this->load->library('user_agent');
        redirectPreUrl(base_url('timekeeping/history'));
    }

    public function listExplanation(){
        if (!(isAdmin() || isLeader()))
            redirect(base_url());
        $where = ($this->input->get('keyword') != null) ? array('fullname like' => '%' . $this->input->get('keyword') . '%') : array();
        $config['params'] = ($this->input->get('keyword') != null) ? array('keyword'    =>  $this->input->get('keyword')) : array();
        if($this->input->get('datefilter')){
            $arrDate = explode(' - ', $this->input->get('datefilter'));
            $date_from = date('Y-m-d',strtotime(str_replace('/', '-', $arrDate[0])));
            $date_to = date('Y-m-d',strtotime(str_replace('/', '-', $arrDate[1])));
            $where['explanation.date_submit >='] = $date_from;
            $where['explanation.date_submit <='] = $date_to;
            $config['params']['datefilter'] = $this->input->get('datefilter');
        }
        $where['explanation.status'] = 0;
        $where['explanation.approver'] = $this->session->id;
        $limit = 6;
        $config['total'] = $this->explanation->countRequestExplanation($where);
        $config['limit'] = $limit;
        $config['page_current'] = ($this->input->get('page') != null) ? $this->input->get('page') : 1;
        $config['url'] = base_url('explanation/listExplanation/');
        $this->load->library('paginator',$config);
        $pagi = $this->paginator;
        $page = $this->paginator->getPage();
        $start = ($page-1)*$limit;
        $requestExplanation = $this->explanation->getRequestExplanation(array($start,$limit),$where);
        $this->render('page.explanation_list', compact('requestExplanation', 'pagi'));
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
            $where['explanation.date_submit >='] = $date_from;
            $where['explanation.date_submit <='] = $date_to;
            $config['params']['datefilter'] = $this->input->get('datefilter');
        }
        $status = ['1','2'];
        if($this->input->get('status') != null && in_array($this->input->get('status'), $status)) {
            $where['explanation.status'] = $this->input->get('status');
            $config['params']['status'] = $this->input->get('status');
        }else{
            $where['explanation.status <>'] = 0;
        }
        $where['explanation.approver'] = $this->session->id;
        $limit = 6;
        $config['total'] = $this->explanation->countRequestExplanation($where);
        $config['limit'] = $limit;
        $config['page_current'] = ($this->input->get('page') != null) ? $this->input->get('page') : 1;
        $config['url'] = base_url('explanation/approved/');
        $this->load->library('paginator',$config);
        $pagi = $this->paginator;
        $page = $this->paginator->getPage();
        $start = ($page-1)*$limit;
        $requestExplanation = $this->explanation->getRequestExplanation(array($start,$limit),$where);
        $this->render('page.explanation_approved', compact('requestExplanation', 'pagi'));
    }

    public function confirm($id){
        $explanation = $this->explanation->getWhere(array('id'    =>  $id));
        if ($explanation['approver'] != $this->session->id){
            redirect(base_url());
        }
        $this->explanation->update(array('status'  =>  '1'), $id);
        redirectPreUrl(base_url('explanation/listExplanation'));
    }

    public function reject($id){
        $explanation = $this->explanation->getWhere(array('id'    =>  $id));
        if ($explanation['approver'] != $this->session->id){
            redirect(base_url());
        }
        $this->explanation->update(array('status'  =>  '2'), $id);
        redirectPreUrl(base_url('explanation/listExplanation'));
    }

}
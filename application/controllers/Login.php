<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
use Jenssegers\Blade\Blade;

class Login extends CI_Controller
{
    private $blade;

    public function __construct()
    {
        parent::__construct();
        $this->blade = new Blade(VIEWPATH, APPPATH . 'cache');
        $this->load->model('user_model','user');
        $this->load->model('timekeeping_model', 'timekeeping');
    }

    public function render($view, $data = array()){
        echo $this->blade->render($view,$data);
    }

    public function requirePass(){
        if (!$this->session->has_userdata('id'))
            redirect(base_url('login'));
        if ($this->session->password !== NULL)
            redirect(base_url());
        $this->render('page.require_pass');
    }

    public function updatePass(){
        if (!$this->session->has_userdata('id'))
            redirect(base_url('login'));
        if ($this->session->password !== NULL)
            redirect(base_url());
        $this->user->update(array('password'    =>  md5($this->input->post('password'))), $this->session->id);
        $this->session->set_userdata('password',md5($this->input->post('password')));
        redirect(base_url());
    }

    public function index(){
        if ($this->session->has_userdata('id'))
            redirect(base_url());
        $error_login = $this->session->flashdata('error_login');
        $this->render('page.login', compact('error_login'));
    }
    public function postLogin()
    {
        if ($this->session->has_userdata('id'))
            redirect(base_url());
        if ($this->input->post('remember')) {
            set_cookie('email', $this->input->post('email'), 60 * 60 * 24 * 30);
            set_cookie('password', $this->input->post('password'), 60 * 60 * 24 * 30);
        } else {
            delete_cookie('email');
            delete_cookie('password');
        }
        $data = array(
            'email' => $this->input->post('email'),
            'password' => md5($this->input->post('password'))
        );
        $user = $this->user->getWhere($data);
        if ($user) {
            $access = (json_decode($user['access_id'], true)) ? json_decode($user['access_id'], true) : array();
            if(isset($access[$this->input->user_agent()])){
                $this->db->delete('ci_sessions', array('id' => $access[$this->input->user_agent()]));
            }
            $this->session->set_userdata($user);
            $access[$this->input->user_agent()] = session_id();
            $access = array('access_id' =>  json_encode($access));
            $this->user->update($access, $user['id']);
            $leader = $this->db->query("select leader from team where id = " . (int)$this->session->id_team)->first_row('array')['leader'];
            $this->session->set_userdata('is_leader',($leader == $this->session->id));
            redirect(base_url());
        } else {
            $this->session->set_flashdata('error_login', 'Email hoặc mật khẩu không chính xác!');
            redirect(base_url('login'));
        }
    }

    public function loginGoogle(){
        if ($this->session->has_userdata('id'))
            redirect(base_url());
        $client = new Google_Client();
        $client->setAuthConfig(APPPATH .'config/client_secret.json');
        $client->addScope("email");
        $client->addScope("profile");
        $service = new Google_Service_Oauth2($client);
        $auth_url = $client->createAuthUrl();
        if (isset($_GET['code'])) {
            $client->authenticate($_GET['code']);
            //$_SESSION['access_token'] = $client->getAccessToken();
            $gg = $service->userinfo->get();
            $user = $this->user->getWhere(array('email'   =>  $gg->email));
            if ($user){
                $access = (json_decode($user['access_id'], true)) ? json_decode($user['access_id'], true) : array();
                if(isset($access[$this->input->user_agent()])){
                    $this->db->delete('ci_sessions', array('id' => $access[$this->input->user_agent()]));
                }
                $this->session->set_userdata($user);
            }else{
                $data = array(
                    'email' =>  $gg->email,
                    'fullname' =>  $gg->name,
                    'role'  =>  1,
                    'image'  =>  'photo.jpg',
                    'password'  =>  NULL,
                    'phone'  =>  NULL,
                    'gender'  =>  NULL,
                    'birthday'  =>  NULL,
                    'address'  =>  NULL,
                    'id_team'  =>  NULL,
                    'access_id'  =>  NULL
                );
                $id = $this->user->insert($data);
                $data['id'] =   $id;
                $this->session->set_userdata($data);
                $access = array();
            }
            $access[$this->input->user_agent()] = session_id();
            $access = array('access_id' =>  json_encode($access));
            $this->user->update($access, $this->session->id);
            $leader = $this->db->query("select leader from team where id = " . (int)$this->session->id_team)->first_row('array')['leader'];
            $this->session->set_userdata('is_leader',($leader == $this->session->id));
            redirect(base_url());
        }else{
            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
        }
    }

}
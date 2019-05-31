<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Jenssegers\Blade\Blade;

class MY_Controller extends CI_Controller
{

    private $blade;

    public function __construct()
    {
        parent::__construct();
        $this->blade = new Blade(VIEWPATH, APPPATH . 'cache');
        if (!$this->session->has_userdata('id'))
            redirect(base_url('login'));
        if ($this->session->password === NULL)
            redirect(base_url('login/requirePass'));
    }

    public function render($view, $data = array()){
        echo $this->blade->render($view,$data);
    }

}
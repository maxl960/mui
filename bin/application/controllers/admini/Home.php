<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	public function __construct(){
        parent::__construct();
    }

    public function index(){
		$this->load->view('admini/header');
		$this->load->view('admini/sider');
        $this->load->view('admini/index');
		$this->load->view('admini/footer');
	}
}
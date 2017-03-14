<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends CI_Controller {
	public function index(){
		redirect(base_url('admini/admin/login'));
	}
}

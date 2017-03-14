<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_Controller extends CI_Controller{
	public function __construct(){
		parent::__construct();
        $token = isset($_SESSION['token'])?$_SESSION['token']:'';
		$this->load->library('common');
		$userinfo = $this->common->decode($token);
		if(!empty($userinfo)){
			$this->load->model('admin_model');
			$obj = $this->admin_model->get_userinfo($userinfo->id);
			if($_SESSION['name'] == $obj['name']){
				$this->member_id = $obj['id'];
			}else{
				redirect(base_url('admini/admin/login'));
				die;
			}
		}else{
		    redirect(base_url('admini/admin/login'));
			die;
		}
	}
}

class Member_Controller extends CI_Controller{
	protected $member_id;
	public function __construct(){
		parent::__construct();
        	
		$this->load->library('common');
		$token = $this->input->post('token');
		$userinfo = $this->common->decode($token);
		if(!empty($userinfo)){
			$this->load->model('user_model');
			$obj = $this->user_model->get_userinfo($userinfo->id);
			if($obj['telephone'] == $userinfo->user && $obj['token'] == $token){
				$this->member_id = $obj['id'];
			}else{
				$msg = array('code' => 400, 'datas'=> array('error' => '无权访问'));	
				echo json_encode($msg);
				die;
			}
		}else{
			$msg = array('code' => 400, 'datas'=> array('error' => '无权访问'));	
			echo json_encode($msg);
			die;
		}
	}
}

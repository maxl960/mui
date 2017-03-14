<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Retbate extends Member_Controller {
	
    public function __construct() {
        parent::__construct();
		$this->load->model('user_model');
        header("Access-Control-Allow-Origin:*");
		$this->load->library('session');
    }
	//发起返利申请
	public function application(){
		$ret['member_id']=$this->member_id;
		$ret['order_sn']=$this->input->post('order_sn');
		if($this->user_model->test_order($ret)){
		$ret['add_time']=time();
		$ret[ 'status' ] = 0;
			$result = $this->user_model->ret_create($ret);
			if($result==1){	
			$data['code'] = 200;
			$data['msg'] = '申请成功，等待管理员处理';
		}else{
			$data['code'] = 400;
			$data['msg'] = '申请失败';
			
		}
		}else{
			$data['code'] = 500;
			$data['msg'] = '该单号已被使用';
		}
		echo json_encode($data,JSON_UNESCAPED_UNICODE);
	}
	
	//查看申请返利列表
	public function ret_list(){
		$result['list'] = $this->user_model->ret_list($this->member_id);
		$result['code'] = 200;
		echo json_encode($result,JSON_UNESCAPED_UNICODE);
	}
	//返利详情
	public function ret_desc(){
		$order_sn = $this->input->post('order_sn');
		$result['list'] = $this->user_model->ret_desc($order_sn);
		$result['code'] = 200;
		echo json_encode($result,JSON_UNESCAPED_UNICODE);
	}
	
	
	
}

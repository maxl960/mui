<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Address extends Member_Controller {
	
    public function __construct() {
        parent::__construct();
		$this->load->model('user_model');
        header("Access-Control-Allow-Origin:*");
		$this->load->library('session');
    }
     //检测用户是否可以添加地址
	private function test_area(){
			$result = $this->user_model->user_area(1);
			if($result<3){
				return true;
			}else{
				return false;
			}
	}
	//列出收货地址
	function list_area(){
		$result = $this->user_model->area_list($this->member_id);
		if($result){
			$data['code'] = 200;
			$data['datas'] = array('address' => $result);
			}else{
			$data['code'] = 400;
			$data['msg'] = '无收获地址';
		}
		echo json_encode($data);
	}
	//添加收货地址
	function create_address(){
		if($this->test_area($this->member_id)){
		$data['member_id'] = $this->member_id;
		$data['consignee'] = $this->input->post('consignee');
		$data['telephone'] = $this->input->post('telephone');
		$data['province'] = $this->input->post('province');
		$data['city'] = $this->input->post('city');
		$data['district'] = $this->input->post('district');
		$data['address'] = $this->input->post('address');
		$data['is_default'] = $this->input->post('is_default');
		$result = $this->user_model->area_create($data);
		if($result>0){
			$dat['code'] = 200;
			$dat['datas'] = '添加成功';
			}else{
			$dat['code'] = 300;
			$dat['msg'] = '无收获地址';
		}
		echo json_encode($dat);
	}else{
		$dat['code'] = 400;
		$dat['msg'] = '只能添加3条收货地址';
		echo json_encode($dat);
	}
	}
	//删除地址
	function delete_area(){
		$data['id'] = $this->input->post('id');
		$data['uid'] = $this->member_id;
		$user=$this->user_model->area_list($data['uid']);
		for($i=0;$i<count($user);$i++){
			if($user[$i]['id']==$data['id'] && $user[$i]['is_default']==1){
				$this->user_model->change_default($data);
			}
		}
		$result = $this->user_model->delete_address($data);
		if($result>0){
			$dat['code'] = 200;
			$dat['datas'] = '删除成功';
			}else{
			$dat['code'] = 400;
			$dat['msg'] = '删除失败';
		}echo json_encode($dat);
	}
	//修改地址
	function edit_address(){
		$data['member_id'] = $this->member_id;
		$data['id'] = $this->input->post('id');
		$data['is_default'] = $this->input->post('is_default');
		$data['consignee'] = $this->input->post('consignee');
		$data['telephone'] = $this->input->post('telephone');
		$data['province'] = $this->input->post('province');
		$data['city'] = $this->input->post('city');
		$data['district'] = $this->input->post('district');
		$data['address'] = $this->input->post('address');
		$result = $this->user_model->edit_area($data);
		$this->user_model->test_default($this->member_id);
		if($result>0){
			$dat['code'] = 200;
			$dat['datas'] = '修改成功';
		}else{
			$dat['code'] = 400;
			$dat['msg'] = '修改失败';
		}echo json_encode($dat);
		
		
	}
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Car extends Member_Controller {
	
    public function __construct() {
        parent::__construct();
		$this->load->model('car_model');
        header("Access-Control-Allow-Origin:*");
		$this->load->library('session');
    }
     //添加购物车
	public function join_car(){
			$data['custom_id'] = $this->member_id;
			$data['product_sn'] = $this->input->post('product_sn');
			$data['attr_id'] = null !== $this->input->post('attr_id')?$this->input->post('attr_id'):null;
			$data['num'] = $this->input->post('num');
			$result = $this->car_model->create($data);
			if($result > 0){
				$data['code'] = 200;
				$data['msg'] = '添加成功';
			}else{
				$data['status'] = 500;
				$data['msg'] = '添加失败';
			}
		echo json_encode($data,JSON_UNESCAPED_UNICODE);
	}
	//购物车修改数量商品
	function edit_num(){
		$data['num'] = $this->input->post('num');
		$data['product_sn'] = $this->input->post('product_sn');
		$data['attr_id'] = null !== $this->input->post('attr_id')?$this->input->post('attr_id'):null;
		$result = $this->car_model->edit_num($data,$this->member_id);
		if($result > 0){
				$data['code'] = 200;
				$data['msg'] = '修改成功';
			}else{
				$data['status'] = 500;
				$data['msg'] = '修改失败';
			}
		echo json_encode($data,JSON_UNESCAPED_UNICODE);
	}
	//删除购物车商品
	function delete_car(){
		$attr_id=null !== $this->input->post('attr_id')?$this->input->post('attr_id'):null;
			$result = $this->car_model->delet($this->input->post('product_sn'),$this->member_id,$attr_id);
			if($result > 0){
				$data['code'] = 200;
				$data['msg'] = '删除成功';
			}else{
				$data['status'] = 500;
				$data['msg'] = '删除失败';
			}
		echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }
	function delete_sn(){
		static $a=0;
		foreach($_POST['sn'] as $key=>$val){
			@$val['attr_id']=null !== $val['attr_id']?$val['attr_id']:null;
			$result = $this->car_model->delet($val['product_sn'],$this->member_id,$val['attr_id']);
		
		$a+=$result;
		}
		if($a == count($_POST['sn'])){
				$data['code'] = 200;
				$data['msg'] = '删除成功';
			}else{
				$data['status'] = 500;
				$data['msg'] = '删除失败';
			}
		echo json_encode($data,JSON_UNESCAPED_UNICODE);
	}
}

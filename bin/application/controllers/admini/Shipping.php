<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shipping extends Admin_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('area_model');
        $this->load->model('shipping_model');  
    }

    /**
	 * 运费列表
	 */
    public function listing(){
		$data['list'] = $this->shipping_model->get_shippinglist();
		$this->load->view('admini/header');
		$this->load->view('admini/sider');
        $this->load->view('admini/shipping/list', $data);
		$this->load->view('admini/footer');
	}
    
    /**
	 * 添加运费
	 */
	public function create(){
        if(!$this->is_validation()){
            $this->area_model->parent_code = 0;
            $data['arealist'] = $this->area_model->get_areabyparent();
            $region = $this->shipping_model->get_shippinglist();
			$arr = '';
			foreach ($region as $val) {
				$arr = $arr.','.$val['region'];
			}
			$data['region'] = explode(',',(substr($arr,1)));

			if(count($data['region']) >= 31){
                echo '<script>alert("全部省份都已设置完成");location.href="'.base_url('admini/shipping/listing').'";</script>';
			}else{
				$this->load->view('admini/header');
				$this->load->view('admini/sider');
				$this->load->view('admini/shipping/create', $data);
				$this->load->view('admini/footer'); 
			}       	
        }else{
			$this->shipping_model->carry_name = $this->input->post('carry_name');
			$this->shipping_model->carry_mode = 1;
			$this->shipping_model->region = implode(',',$this->input->post('region'));
			$this->shipping_model->first_weight = $this->input->post('first_weight');
			$this->shipping_model->first_price = $this->input->post('first_price');
			$this->shipping_model->second_weight = $this->input->post('second_weight');
			$this->shipping_model->second_price = $this->input->post('second_price');
			$this->shipping_model->free_amount = $this->input->post('free_amount');

			$result = $this->shipping_model->create();
			if($result > 0){  
				echo '<script>alert("信息提交成功");location.href="'.base_url('admini/shipping/create').'";</script>';
			}
		}
	}

	/**
	 * 编辑运费
	 */
	public function edit($id){
        if(!$this->is_validation()){
            $this->area_model->parent_code = 0;
            $data['arealist'] = $this->area_model->get_areabyparent();
            
			$this->shipping_model->id = $id;
            $region = $this->shipping_model->get_editregoin();
			$arr = '';
			foreach ($region as $val) {
				$arr = $arr.','.$val['region'];
			}
			$data['region'] = explode(',',(substr($arr,1)));

			$data['shipping'] = $this->shipping_model->get_shippingbyid();
            $data['selecedregion'] = explode(',',($data['shipping']['region']));
		
            $this->load->view('admini/header');
            $this->load->view('admini/sider');
            $this->load->view('admini/shipping/edit', $data);
            $this->load->view('admini/footer');        	
        }else{
			$this->shipping_model->carry_name = $this->input->post('carry_name');
			$this->shipping_model->carry_mode = 1;
			$this->shipping_model->region = implode(',',$this->input->post('region'));
			$this->shipping_model->first_weight = $this->input->post('first_weight');
			$this->shipping_model->first_price = $this->input->post('first_price');
			$this->shipping_model->second_weight = $this->input->post('second_weight');
			$this->shipping_model->second_price = $this->input->post('second_price');
			$this->shipping_model->free_amount = $this->input->post('free_amount');
            $this->shipping_model->id = $id;

			$result = $this->shipping_model->update();
			if($result > 0){  
				echo '<script>alert("信息提交成功");location.href="'.base_url('admini/shipping/listing').'";</script>';
			}else{
                echo '<script>alert("未做修改");location.href="'.base_url('admini/shipping/listing').'";</script>';
            }
		}
	}

	/**
	 * 删除运费
	 */
	public function delete($id){
		$this->shipping_model->id = $id;
		$result = $this->shipping_model->delete();
		if($result > 0){
			redirect(base_url('admini/shipping/listing'));
		}
	}

    /**
	 * 输入验证
	 */
	private function is_validation(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('carry_name', '模板名称', 'required',
            array('required' => '必须输入模板名称')
        );
        return $this->form_validation->run();
	}	
}
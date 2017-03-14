<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity extends Admin_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('activity_model');
        $this->load->model('goods_model');
        $this->load->library('common'); 
    }

    /**
	 * 添加活动
	 */
    public function create($gid = 0){
        if(!$this->is_validation()){
            if($gid <= 0){
                echo '<script>alert("请选择一个商品");location.href="'.base_url('admini/goods/listing').'";</script>';
                die;
            }
            $this->goods_model->id = $gid;
			$data['goods'] = $this->goods_model->get_goodsbyid();
			
            $this->load->view('admini/header');
            $this->load->view('admini/sider');
            $this->load->view('admini/activity/create',$data);
            $this->load->view('admini/footer');        	
        }else{
            $this->activity_model->goods_id = $this->input->post('goods_id');
            $this->activity_model->act_name = $this->input->post('act_name');
            $act_type = $this->input->post('act_type');
			$this->activity_model->act_type = $act_type;
			$this->activity_model->act_price = $this->input->post('act_price');
			$this->activity_model->act_stock = $this->input->post('act_stock');
			$this->activity_model->act_weight = $this->input->post('act_weight');
			$this->activity_model->start_time = strtotime($this->input->post('start_time'));
			$this->activity_model->end_time = strtotime($this->input->post('end_time'));
	        $this->activity_model->group_number = $this->input->post('group_number');
			$this->activity_model->rebate_number = $this->input->post('rebate_number');
	        $this->activity_model->rebate_rate = $this->input->post('rebate_rate');
			$this->activity_model->rebate_cycle = $this->input->post('rebate_cycle');
            $this->activity_model->frequency = $this->input->post('frequency');

    		$doc = 'temp';
			$updata = $this->common->upload_image($doc);
			if(!empty($updata['thumb']['file_name'])){
				$source = 'uploads/'.$doc.'/'.$updata['thumb']['file_name'];
				$newpath = 'uploads/goods_t/';
				$suffix = '_t';
				$raw_name = $updata['thumb']['raw_name'];
				$ext = $updata['thumb']['file_ext'];
				$this->common->thumb($source, $newpath, $suffix,$width = 300,$height = 300);
				$this->activity_model->act_thumb = 'uploads/goods_t/'.$raw_name.$suffix.$ext;
			}
		
			$result = $this->activity_model->create();
			if($result > 0){
				echo '<script>alert("信息提交成功");location.href="'.base_url('admini/activity/listing/'.$act_type).'";</script>';
			}
		}
    }
    
    /**
	 * 编辑活动
	 */
    public function edit($act_id = 0){
        if(!$this->is_validation()){
            $this->activity_model->id = $act_id;
			$data['activity'] = $this->activity_model->get_activitybyid();
			
            $this->load->view('admini/header');
            $this->load->view('admini/sider');
            $this->load->view('admini/activity/edit',$data);
            $this->load->view('admini/footer');        	
        }else{
            $act_type = $this->input->post('act_type');
            $this->activity_model->act_name = $this->input->post('act_name');
			$this->activity_model->act_price = $this->input->post('act_price');
			$this->activity_model->act_stock = $this->input->post('act_stock');
			$this->activity_model->act_weight = $this->input->post('act_weight');
			$this->activity_model->start_time = strtotime($this->input->post('start_time'));
			$this->activity_model->end_time = strtotime($this->input->post('end_time'));
	        $this->activity_model->group_number = $this->input->post('group_number');
			$this->activity_model->rebate_number = $this->input->post('rebate_number');
	        $this->activity_model->rebate_rate = $this->input->post('rebate_rate');
			$this->activity_model->rebate_cycle = $this->input->post('rebate_cycle');
            $this->activity_model->frequency = $this->input->post('frequency');
            $this->activity_model->id = $act_id;

    		$doc = 'temp';
			$updata = $this->common->upload_image($doc);
			if(!empty($updata['thumb']['file_name'])){
				$source = 'uploads/'.$doc.'/'.$updata['thumb']['file_name'];
				$newpath = 'uploads/goods_t/';
				$suffix = '_t';
				$raw_name = $updata['thumb']['raw_name'];
				$ext = $updata['thumb']['file_ext'];
				$this->common->thumb($source, $newpath, $suffix,$width = 300,$height = 300);
				$this->activity_model->act_thumb = 'uploads/goods_t/'.$raw_name.$suffix.$ext;
			}
			$result = $this->activity_model->update();
			if($result > 0){
				echo '<script>alert("信息提交成功");location.href="'.base_url('admini/activity/listing/'.$act_type).'";</script>';
			}else{
                echo '<script>alert("未做修改");location.href="'.base_url('admini/activity/listing/'.$act_type).'";</script>';
            }
		}
    }

    /**
	 * 活动列表
	 */
    public function listing($typeid, $page = 1){
        $limit = 15;
		$offset = ($page-1)*$limit;
        $this->activity_model->act_type = $typeid;
		$rows = intval($this->activity_model->get_total());
		$totalpage = ceil($rows/$limit);
		
		$this->load->library('pagination');
		$config['base_url'] = 'admini/activity/listing/'.$typeid;
        $config['first_url'] ='admini/activity/listing/'.$typeid.'/1';
		$config['total_rows'] = $rows;
		$config['per_page'] = $limit;
		$this->pagination->initialize($config);
	
		$list = $this->activity_model->get_activitylist($limit, $offset);
		$data['list'] = $list;

        $this->load->view('admini/header');
        $this->load->view('admini/sider');
        $this->load->view('admini/activity/listing',$data);
        $this->load->view('admini/footer');        	
    }
    
    /**
	 * 活动上下架
	 */
	public function set_status($id, $status, $tid){
		if($status == 0){
			$status = 1;
		}else{
			$status = 0;
		}
		$result = $this->activity_model->set_status($id, $status);
		if($result > 0){
            echo '<script>alert("信息提交成功");location.href="'.base_url('admini/activity/listing/'.$tid).'";</script>';
		}
	}

    /**
	 * 输入验证
	 */
	private function is_validation(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('act_name', '活动名称', 'required',
            array('required' => '必须输入活动名称')
        );
        return $this->form_validation->run();
	}	
}
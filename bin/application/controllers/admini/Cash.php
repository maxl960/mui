<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cash extends Admin_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('cash_model'); 
    }

    /**
	*获取体现申请列表
	*/
	public function application_list($page = 1){
		$limit = 30;
		$offset = ($page-1)*$limit;
		$rows = intval($this->cash_model->get_application_total());
		$totalpage = ceil($rows/$limit);
		
		$this->load->library('pagination');
		$config['base_url'] = 'admini/cash/application_list/';
		$config['total_rows'] = $rows;
		$config['per_page'] = $limit;
		$this->pagination->initialize($config);
	
		$list = $this->cash_model->get_application_list($limit, $offset);
		$data['list'] = $list;
		$this->load->view('admini/header');
		$this->load->view('admini/sider');
        $this->load->view('admini/users/cash_applist',$data);
		$this->load->view('admini/footer');
	}

    /**
    *提现申请处理
    */
	public function set_application_status(){
		$this->cash_model->id = $this->input->post('id');
		$status = $this->input->post('status');
		$this->cash_model->status = $status;
		$member_id = $this->input->post('member_id');
		$amount = $this->input->post('amount');
		$this->db->trans_start();
		$result = $this->cash_model->set_application_status();
		if($result > 0){
            if($status == 1){
				$this->load->model('user_model');
				$this->user_model->member_id = $member_id;
				$this->user_model->amount_minus($amount);
				$this->load->model('log_model');
				$this->log_model->operation_type = 4;
				$this->log_model->content = '用户提现'.$amount.'元';
				$this->log_model->amount = $amount;
				$this->log_model->member_id = $member_id;
				$this->log_model->operator = 0;
				$this->log_model->create();
			}
			$this->db->trans_complete();
			$msg = array('code' => 200, 'msg' => '信息提交成功');	
			echo json_encode($msg);
		}else{
			$msg = array('code' => 400, 'error' => '信息提交失败');	
			echo json_encode($msg);
		}
	}
}
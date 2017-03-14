<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Operation extends Admin_Controller {
	public function __construct(){
        parent::__construct();  
        $this->load->model('log_model');
    }

    /**
    *@param $operation 操作类型
	*订单列表
	*/
    public function listing($operation = 6, $page = 1){
        $limit = 30;
		$offset = ($page-1)*$limit;
        
        $this->log_model->operation_type = $operation;
		$rows = intval($this->log_model->get_total());
		$totalpage = ceil($rows/$limit);
		
		$this->load->library('pagination');
		$config['base_url'] = 'admini/operation/listing/'.$operation;
		$config['first_url'] = 'admini/operation/listing/'.$operation.'/1';
		$config['total_rows'] = $rows;
		$config['per_page'] = $limit;
		$this->pagination->initialize($config);
        
		$query = $this->log_model->get_log_list($limit, $offset);
        $data['list'] = $query;

        $this->load->view('admini/header');
		$this->load->view('admini/sider');
        $this->load->view('admini/operation/list',$data);
		$this->load->view('admini/footer');
    }
}
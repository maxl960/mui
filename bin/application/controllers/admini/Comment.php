<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comment extends Admin_Controller {
	public function __construct(){
        parent::__construct();  
        $this->load->model('comment_model');
    }
    
    /**
    *评价列表
    */
    public function listing($page = 1){
		$limit = 15;
		$offset = ($page-1)*$limit;
		$rows = intval($this->comment_model->get_total());
		$totalpage = ceil($rows/$limit);
		
		$this->load->library('pagination');
		$config['base_url'] = 'admini/comment/listing/';
		$config['total_rows'] = $rows;
		$config['per_page'] = $limit;
		$this->pagination->initialize($config);
	
		$list = $this->comment_model->get_commentlist($limit, $offset);
		$data['list'] = $list;
		$this->load->view('admini/header');
		$this->load->view('admini/sider');
        $this->load->view('admini/comment/list',$data);
		$this->load->view('admini/footer');
	}

	/**
	*修改显示状态
	*/
	public function change_status($id,$status){
		if($status == 0){
			$this->comment_model->is_show = 1;
		}else{
			$this->comment_model->is_show = 0;
		}
		$this->comment_model->id = $id;
		$result = $this->comment_model->set_status();
		if($result > 0){
			redirect(base_url('admini/comment/listing'));
		}
	}

	/**
	*评论回复
	*/
	public function reply($id){
		$this->comment_model->id = $id;
		$this->comment_model->reply = $this->input->post('reply');
		$result = $this->comment_model->set_reply();
		if($result > 0){
			redirect(base_url('admini/comment/listing'));
		}
	}
}
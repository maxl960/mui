<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rebate extends Admin_Controller {
	
    public function __construct() {
        parent::__construct();
		$this->load->model('admin_model');
		$this->load->library('session');
		$this->load->library('common');
    }
    
	//清理字符串里面的空格反斜杠
	function remove_html_tag($str)
	{  //清除HTML代码、空格、回车换行符
        //trim 去掉字串两端的空格
        //strip_tags 删除HTML元素 
        $str = trim($str);
        $str = @preg_replace('/<script[^>]*?>(.*?)<\/script>/si', '', $str);
        $str = @preg_replace('/<style[^>]*?>(.*?)<\/style>/si', '', $str);
        $str = @strip_tags($str,"");
        $str = @ereg_replace("\t","",$str);
        $str = @ereg_replace("\r\n","",$str);
        $str = @ereg_replace("\r","",$str);
        $str = @ereg_replace("\n","",$str);
        $str = @ereg_replace(" ","",$str);
        $str = @ereg_replace("&nbsp;","",$str);
        return trim($str);
    } 
	function ad_add(){
		$this->load->view('admini/header');
			$this->load->view('admini/sider');
			$this->load->view('admini/ad/create');
			$this->load->view('admini/footer');
	}
	
	/*返利列表*/
	function rebate_list($page=1){
			$limit = 15;
			$offset = ($page-1)*$limit;
			$rows = intval($this->admin_model->rebate_total());
			$totalpage = ceil($rows/$limit);
		
			$this->load->library('pagination');
			$config['base_url'] = 'admini/rebate/rebate_list/';
			$config['total_rows'] = $rows;
			$config['per_page'] = $limit;
			$this->pagination->initialize($config);
	
			$data['list'] = $this->admin_model->rebate_list($limit, $offset);
			$this->load->view('admini/header');
			$this->load->view('admini/sider');
			$this->load->view('admini/rebate/listing',$data);
			$this->load->view('admini/footer');
	}
	
	/*拒绝返利*/
	function refuse(){
		$data['order_sn']=$this->input->post('order_sn');
		$data['remark']=$this->input->post('reason');
		$data['status']=-1;
		$this->admin_model->rebate_refuse($data);
			redirect(base_url('admini/rebate/rebate_list'));
	}
	/*添加返利规则*/
	function create_rebate(){
		$data['order_sn']=$this->input->post('order_sn');
		$result = $this->admin_model->rebate_data($data['order_sn']);
		$data['member_id'] = $result['member_id'];
		$data['amount']=$this->input->post('amount');
		$data['rebate_cycle']=$this->input->post('rebate_cycle');
		$data['frequency']=$this->input->post('frequency');
		$data['previous_rebate'] = floor($data['amount']/$data['frequency']);
		$data['last_rebate'] = $data['amount'] - (($data['frequency'] - 1) * $data['previous_rebate']);
		$data['returned'] = 0;
        $data['start_time'] = time();
        $data['end_time'] = time() + 3600 * 24 * $data['frequency'] * $data['rebate_cycle'];
		$data['operator']='管理员';
		$this->admin_model->rebate_create($data);
		redirect(base_url('admini/rebate/rebate_list'));
	}
}

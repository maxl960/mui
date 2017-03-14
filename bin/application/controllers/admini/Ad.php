<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ad extends Admin_Controller {
	
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
	
	/*广告列表*/
	function ad_list($page=1){
			$limit = 15;
			$offset = ($page-1)*$limit;
			$rows = intval($this->admin_model->getad_total());
			$totalpage = ceil($rows/$limit);
		
			$this->load->library('pagination');
			$config['base_url'] = 'admini/advertisement/ad_list/';
			$config['total_rows'] = $rows;
			$config['per_page'] = $limit;
			$this->pagination->initialize($config);
	
			$data['list'] = $this->admin_model->ad_list($limit, $offset);
			$this->load->view('admini/header');
			$this->load->view('admini/sider');
			$this->load->view('admini/ad/list',$data);
			$this->load->view('admini/footer');
	}
	/*广告添加*/
	function ad_create(){
		$this->load->model('pictures_model');
		$data['ad_name']=$this->input->post('name');
		$data['ad_link']=$this->input->post('link');
		$data['enabled']=$this->input->post('enabled')==null?0:$this->input->post('enabled');
		$doc = 'temp';
			$updata = $this->common->upload_image($doc);
			if(!empty($updata['thumb']['file_name'])){
				$source = 'uploads/'.$doc.'/'.$updata['thumb']['file_name'];
				$newpath = 'uploads/ad/';
				$suffix = '_t';
				$raw_name = $updata['thumb']['raw_name'];
				$ext = $updata['thumb']['file_ext'];
				$this->common->thumb($source, $newpath, $suffix,$width = 300,$height = 300);
				$data['picurl'] = 'uploads/ad/'.$raw_name.$suffix.$ext;
			}
		$result = $this->admin_model->ad_create($data);
		if($result>0){
			echo '<script>alert("信息提交成功");location.href="'.base_url('admini/ad/ad_list').'";</script>';
		}
	}
	//广告状态修改
	function status_change($id,$status){
		$num=$this->admin_model->ad_active();
		if($num>0 && $num<5 && $status==1){
		$this->admin_model->ad_id=$id;
		$this->admin_model->ad_status=$status==0?1:0;
		$result = $this->admin_model->status_change();
			redirect(base_url('admini/ad/ad_list'));
		}
	}
	/*广告编辑*/
	function ad_update($id){
		$result = $this->admin_model->show_ad($id);
		$this->load->view('admini/header');
			$this->load->view('admini/sider');
			$this->load->view('admini/ad/edit',$result);
			$this->load->view('admini/footer');
	}
	function ad_edit(){
		$this->load->model('pictures_model');
		$data['id']=$this->input->post('id');
		$data['ad_name']=$this->input->post('name');
		$data['ad_link']=$this->input->post('link');
		$data['enabled']=$this->input->post('enabled')==null?0:$this->input->post('enabled');
		$doc = 'temp';
			$updata = $this->common->upload_image($doc);
			if(!empty($updata['thumb']['file_name'])){
				$source = 'uploads/'.$doc.'/'.$updata['thumb']['file_name'];
				$newpath = 'uploads/ad/';
				$suffix = '_t';
				$raw_name = $updata['thumb']['raw_name'];
				$ext = $updata['thumb']['file_ext'];
				$this->common->thumb($source, $newpath, $suffix,$width = 60,$height = 60);
				$data['picurl'] = 'uploads/ad/'.$raw_name.$suffix.$ext;
			}
		$result = $this->admin_model->ad_edit($data);
		if($result>0){
			echo '<script>alert("信息提交成功");location.href="'.base_url('admini/ad/ad_list').'";</script>';
		}else{
			redirect(base_url('admini/ad/ad_list'));
		}
	}
	/*广告删除*/
	function ad_delete($id){
		
		$this->admin_model->ad_id=$id;
		$result = $this->admin_model->ad_delete();
			redirect(base_url('admini/ad/ad_list'));
	}
	//客服列表
	function list_service(){
		$data['list'] = $this->admin_model->service_list();
		$this->load->view('admini/header');
		$this->load->view('admini/sider');
		$this->load->view('admini/ad/service_list',$data);
		$this->load->view('admini/footer');
	}
	//客服添加
	function create_service(){
		$this->load->view('admini/header');
		$this->load->view('admini/sider');
		$this->load->view('admini/ad/service_create');
		$this->load->view('admini/footer');
	}
	function add_service(){
		$data['name'] = $this->remove_html_tag($this->input->post('name'));
		$data['telephone'] = $this->input->post('tel');
		$data['password'] = md5($this->input->post('password'));
		$data['status'] =$this->input->post('status');
		$result = $this->admin_model->create_service($data);
		if($result>0){
			echo '<script>alert("信息提交成功");location.href="'.base_url('admini/ad/list_service').'";</script>';
		}
	}
	//客服修改
	function desc_service($id){
		$data = $this->admin_model->service_desc($id);
		$this->load->view('admini/header');
		$this->load->view('admini/sider');
		$this->load->view('admini/ad/service_edit',$data);
		$this->load->view('admini/footer');
	}
	function edit_service(){
		$data = $this->input->post();
		$result = $this->admin_model->service_desc($data['id']);
		if($data['password'] != $result['password']){
			$data['password'] = md5($data['password']);
		}
		$res = $this->admin_model->service_update($data);
		if($res == 1){
			echo '<script>alert("信息修改成功");location.href="'.base_url('admini/ad/list_service').'";</script>';
		}else{
			echo '<script>alert("信息未做修改");location.href="'.base_url('admini/ad/list_service').'";</script>';
		}
		
	}
	//客服删除
	function service_delete($tel){
		$result = $this->admin_model->del_service($tel);
		if($result>0){
			echo '<script>alert("信息提交成功");location.href="'.base_url('admini/ad/list_service').'";</script>';
		}
	}
	//消息列表
	function message_list(){
		$data['list'] = $this->admin_model->service_list();
		if(is_numeric($this->input->post('server'))  && is_numeric($this->input->post('custom'))){
			$data['server'] = $this->input->post('server');
			$data['name'] = $this->admin_model->server_name($data['server']);
			$data['custom'] = $this->input->post('custom');
			$data['mlist'] = $this->admin_model->message_list($data);
		}
		$this->load->view('admini/header');
		$this->load->view('admini/sider');
		$this->load->view('admini/ad/message_list',$data);
		$this->load->view('admini/footer');
	}
	//获取用户
	function custom_list(){
		$ser = $this->input->post('user');
		$cus = $this->admin_model->find_cus($ser);
		$temp = array();
		$tem = array();
		foreach ($cus as $v){
		$v = join(",",$v);
		$temp[] = $v;
		}
		foreach ($temp as $k){
		$tem[] = $k;}
		$tem = implode(",",$tem);
		$temp = str_replace($ser,"",$tem);
		$temp = explode(",",$temp);
		$temp = array_unique($temp);
		foreach( $temp as $a=>$e){  
		if( !$e ){
			unset( $temp[$a] ); } 
		} 
		$temp = implode(",", $temp);
		$result = explode(",",$temp);
		echo json_encode($result);
	}
}

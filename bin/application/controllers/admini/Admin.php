<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
	
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
	
	
	//用户、公司登录
	public function login(){
		session_unset();
		session_destroy();
		$this->load->view('admini/login');
	}

	public function logout(){
		session_unset();
		session_destroy();
		redirect(base_url('admini/admin/login'));	
	}

	/*登录*/
	function adlogin(){
			$userdata['name']=$this->remove_html_tag($this->input->post('name'));
			$userdata['pwd']=md5($this->input->post('pwd'));
			$result = $this->admin_model->adminlogin($userdata);
			if($result){
			
			$newdata = array(
					   'token'  => $result['token'],
					   'name' => $userdata['name']
				   );
			$this->session->set_userdata($newdata);
		    redirect(base_url('admini/goods/listing'));
		}else{
			$data['message'] = '用户名或密码错误';
		    $data['url'] = base_url('admini/admin/login');
			$this->load->view('message',$data);
		}
	}
	/*客服登录*/
	function serlogin(){
		$this->load->view('admini/slogin');
	}
	function slogin(){
		$data=$this->input->post();
		$result = $this->admin_model->slogin($data);
		if($result){
			//var_dump($result);
			$newdata = array(
					   'token'  => $result['token'],
					   'telephone' => $data['telephone']
				   );
			$this->session->set_userdata($newdata);
			redirect(base_url('admini/chat/home'));
		}else{
			$data['message'] = '登录失败';
		    $data['url'] = base_url('admini/admin/serlogin');
			$this->load->view('message',$data);
		}
	}
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Admin_Controller {
	
    public function __construct() {
        parent::__construct();
		$this->load->model('admin_model');
		$this->load->library('session');
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
	
	/*后台中心*/
	function userlist($page=1){
			$limit = 15;
			$offset = ($page-1)*$limit;
			$rows = intval($this->admin_model->getuser_total());
			$totalpage = ceil($rows/$limit);
		
			$this->load->library('pagination');
			$config['base_url'] = 'admini/user/userlist/';
			$config['total_rows'] = $rows;
			$config['per_page'] = $limit;
			$this->pagination->initialize($config);
	
			$data['list'] = $this->admin_model->userpre($limit, $offset);
			
			for($i=0;$i<count($data['list']);$i++){
				$data['list'][$i]['reg_time']=date('Y-m-d',$data['list'][$i]['reg_time']);
			}
			$this->load->view('admini/header');
			$this->load->view('admini/sider');
			$this->load->view('admini/users/list',$data);
			$this->load->view('admini/footer');
	}
	
}

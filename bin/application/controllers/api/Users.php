<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
	
    public function __construct() {
        parent::__construct();
		$this->load->model('user_model');
        header("Access-Control-Allow-Origin:*");
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
	
	public function register(){
		$create['client_id']=$this->input->post('cid');
		$create['telephone']=$this->input->post('telephone');
		$create['password']=$this->input->post('password');
		$create [ 'user_group' ] = 0;
			$result = $this->user_model->create($create);
			if($result){	
				$this->userlogin($create['telephone'],$create['password'],$create['client_id']);
		}else{
			$data['status'] = 0;
			$data['msg'] = '注册失败';
			echo json_encode($data,JSON_UNESCAPED_UNICODE);
		}
		
	}
	//手机短信验证码
	function authentication(){	
	$tel = $this->input->post('tel');
	unset($_SESSION[$tel]);
	include "TopSdk.php";
    date_default_timezone_set('Asia/Shanghai'); 
	$_SESSION[$tel]=mt_rand(1000,9999);
	$c=new TopClient;
	$c->appkey = '23559508';
	$c->secretKey = '94f657672f3eb3160c824c35ea150bbb';
	$req = new AlibabaAliqinFcSmsNumSendRequest;
	$req->setExtend("");
	$req ->setSmsType( "normal" );
	$req ->setSmsFreeSignName( "伴买伴送" );
	$req ->setSmsParam( "{code:'".$_SESSION[$tel]."',product:'伴买伴送'}" );
	$req ->setRecNum( $tel );
	$req ->setSmsTemplateCode( "SMS_33700079" );
	$resp = $c ->execute( $req ); 
	echo json_encode($this->xmlToArr($resp,$root = false));
	}
	public function xmlToArr($xml, $root = true)
	{


		if(!$xml->children())
		{
			return (string)$xml;
		}
		$array = array();
		foreach($xml->children() as $element => $node)
		{
			$totalElement = count($xml->{$element});
			if(!isset($array[$element]))
			{
				$array[$element] = "";
			}
			// Has attributes
			if($attributes = $node->attributes())
			{
				$data = array('attributes' => array(), 'value' => (count($node) > 0) ? $this->xmlToArr($node, false) : (string)$node);
				foreach($attributes as $attr => $value)
				{
					$data['attributes'][$attr] = (string)$value;
				}
				if($totalElement > 1)
				{
					$array[$element][] = $data;
				}
				else
				{
					$array[$element] = $data;
				}
				// Just a value
			}
			else
			{
				if($totalElement > 1)
				{
					$array[$element][] = $this->xmlToArr($node, false);
				}
				else
				{
					$array[$element] = $this->xmlToArr($node, false);
				}
			}
		}
		if($root)
		{
			return array($xml->getName() => $array);
		}
		else
		{
			return $array;
		}


	}
	//验证短信验证码
	function IsCodeCorrect(){
		$utel = $this->remove_html_tag($_POST['data']);
		$data = explode(",",$utel);
		
		$tel = $data[0];
		$code = $data[1];
		$cor = $_SESSION[$tel];
		if($code==$cor){
			$res = 1;
		}else{
			$res =0;
		}
		echo $res;
	}
	//验证手机号是否存在
	function IsTelExist(){	
		$utel = $this->remove_html_tag($_POST['data']);		
		if(!empty($utel)){
			$ret = $this->user_model->telexist($utel)?1:0;
		}else{
		$ret = 0;
		}
		echo $ret;
    }

	 /**
	* 登录
	* @return json
	自动登录*/
	function autologin(){
		$userdata=$this->input->post('token');
		$result = $this->user_model->autologin($userdata);
		if($result){
			$data = $result;
			$data['status'] = 1;
			$data['msg'] = '登录成功';
		}else{
			$data['status'] = 0;
			$data['msg'] = '登录失败';
		}
		echo json_encode($data,JSON_UNESCAPED_UNICODE);
	}
	
	/*登录*/
	function userlogin($lname = 0,$lpass = 0,$lcid = 0){
		if($lname == 0){
			$userdata['telephone']=$this->input->post('telephone');
			$userdata['password']=$this->input->post('password');
			$userdata['cid']=$this->input->post('cid');
			$result = $this->user_model->login($userdata);
		}else{
			$userdata['telephone']=$lname;
			$userdata['password']=$lpass;
			$userdata['cid']=$lcid;
			$result = $this->user_model->login($userdata);
		}
			if($result){
			$data['status'] = 1;
			$data['telephone'] = $result['telephone'];
			$data['token'] = $result['token'];
			$data['msg'] = '登录成功';
		}else{
			$data['status'] = 0;
			$data['msg'] = '登录失败';
		}
		echo json_encode($data,JSON_UNESCAPED_SLASHES);	
		}
	
	/**
	* 修改密码
	* @return json
	个人*/
	function editpass(){
		$userdata['telephone']=$this->input->post('telephone');
		$userdata['password']=$this->input->post('password');
		$result = $this->user_model->editpass($userdata);
		if($result){
			$data['status'] = 1;
			$data['msg'] = '修改密码成功';
		}else{
			$data['status'] = 0;
			$data['msg'] = '修改密码失败或密码与原密码相同';
		}
		echo json_encode($data,JSON_UNESCAPED_UNICODE);	
	}
	
}

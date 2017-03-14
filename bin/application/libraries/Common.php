<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Common{
	protected $CI;
        
    public function __construct(){
        $this->CI =& get_instance();
    }
    
	/**
	 * token加密
	 * @param mixed $data
	 *
	 * @return string
	 */
	public function encode($data){
		$this->CI->encryption->initialize(array('cipher' => 'DES'));
		$token = $this->CI->encryption->encrypt(json_encode($data));
		return $token;		 
	}
	
	/**
	 * token解密
	 * @param mixed $token
	 *
	 * @return object
	*/
	public function decode($token){
		$this->CI->encryption->initialize(array('cipher' => 'DES'));
		$obj = $this->CI->encryption->decrypt($token);
		return json_decode($obj);
	}
	/**
	* 登录验证
	* @param token
	*/
    public function is_auth($token){
		$isauth=0;
		$userinfo = $this->decode($token);
		if(!empty($userinfo)){
			$this->CI->load->model('user_model');
			$obj = $this->CI->user_model->get_userinfo($userinfo->id);
			$user = $obj['name'];
			if($user == $userinfo->user){
				$isauth = $obj['id'];
			}
		}
		return $isauth;
	}  		 

	/**
	 * 图片上传 
	 */
	public function upload_image($doc = 'temp'){
		$path = 'uploads/'.$doc.'/';
		$updata = array();
    	foreach($_FILES as $file=>$data){
    		if(!empty($data['name'])){
	    		$config = array(
					'upload_path'  =>  $path,
					'allowed_types'=> 'gif|bmp|jpg|jpeg|png',
					'file_name'    => md5($data['name'].time()),
				);
				$this->CI->upload->initialize($config);
				if($this->CI->upload->do_upload($file)){
					if($file=='thumb'){
						$updata['thumb'] = $this->CI->upload->data();
					}else{
						$updata[] = $this->CI->upload->data();
					}		 
				}
				else{
					echo $this->CI->upload->display_errors();
				}
			}
    	}
		return $updata;
	}
 
	/**
	 * 生成缩略图
	 */
	public function thumb($source, $newpath, $newname,$width = 640,$height = 480){
		$config['image_library'] = 'gd2';
		$config['source_image'] = $source;
		$config['create_thumb'] = TRUE;
		$config['maintain_ratio'] = FALSE;
		$config['width'] = $width;
		$config['height'] = $height;
		$config['new_image'] = $newpath;
		$config['thumb_marker'] = $newname;
    
		$this->CI->image_lib->initialize($config);
		if(!$this->CI->image_lib->resize()){
			echo $this->CI->image_lib->display_errors();
		}
	}

	/**
	 * 获取某个分类的所有子分类
	 */
	static public function get_subs($categorys,$catid=0,$level=1){
		$subs=array();
		foreach($categorys as $item){
			if($item['parentid']==$catid){
				$item['level']=$level;
				$subs[]=$item;
				$subs=array_merge($subs,self::get_subs($categorys,$item['id'],$level+1));
			}	
		}
		return $subs;
	}

	static public function recursion($data, $id = 0) {
		$list = array();
		foreach($data as $v) {
			if($v['parentid'] == $id) {
				$v['son'] = self::recursion($data, $v['id']);
				if(empty($v['son'])) {
					unset($v['son']);
				}
					array_push($list, $v);
				}
			}
		return $list;
	}	
}
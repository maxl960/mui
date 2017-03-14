<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Update extends CI_Controller {
	private $CUR_VERSION = '1.0.1';
	public function __construct() {
		parent::__construct();
	}
	/**
	*检查升级信息
	*@param $version 客户端版本号
	*@return json
	*/
	public function index(){
		$os = $this->input->post('os');
		$version = $this->input->post('version');
		switch($os){
			case "Android":
			$ext = ".apk";
			break;
			case "IOS":
			$ext = ".ipa";
			break;
			default:
			$ext = ".apk";
			break;
		}
		$a1 = explode('.',$this->CUR_VERSION);
		$a2 = empty($version) ? 0 : explode('.',$version);
		$len = count($a2);
		$isnew = 1;
		$url = '';
		for($i = 0; $i < $len; $i++){
			if($a1[$i] > $a2[$i]){
				$isnew = 0;
				$url = 'http://www.banmaibansong.com/update/bmbs'.$ext;
				break;
			}
		}
		
		$data['isnew'] = $isnew;
		$data['url'] = $url;
		echo json_encode($data);
	}
}

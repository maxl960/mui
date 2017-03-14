<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends CI_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('category_model');
    }
    
    /**
	 * 获取某分类的子分类信息
	 * @param cid 0=顶级
	 * @return json
	 */
	public function get_categorys($cid = 0){
    	$this->category_model->cat_id = $cid;
    	$this->category_model->is_show = 1;
        $list = $this->category_model->get_categorysbyparent();
		$arr = array();
		foreach ($list as $key => $val) {
			$arr[$key]['id'] = $val['id'];
			$arr[$key]['cat_name'] = $val['cat_name'];
			$arr[$key]['icon'] = $val['icon'];		
		}
    	$data['code'] = 200;
		$data['datas'] = array('categorys' => $arr);	
		echo json_encode($data);
	}
	
	/**
	 * 获取全部分类信息列表
	 * @return json
	 */
	public function get_categorylist(){
		$this->category_model->is_show = 1;
		$data = array();
	    $list = $this->category_model->get_categorys();
		$arr = array();
		foreach ($list as $key => $val) {
			$arr[$key]['id'] = $val['id'];
			$arr[$key]['cat_name'] = $val['cat_name'];
			$arr[$key]['icon'] = $val['icon'];
		}
		$data['code'] = 200;
		$data['datas'] = array('categorys' => $arr);	
	    echo json_encode($data);    
	}
	
	/**
	 * 获取分类信息树
	 * @return json
	 */
	public function get_allcategorys(){
		$this->category_model->is_show = 1;
		$list = $this->category_model->get_categorys();
		$this->load->library('common');
		$data = array();
    	$data['code'] = 200;
	    $data['datas'] = array('categorys' => common::recursion($list,0));
	    echo json_encode($data);    
	}
}
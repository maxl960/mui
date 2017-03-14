<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends Admin_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('category_model');
		$this->load->library('common'); 
    }
    
	/**
	 * 添加分类
	 */
	public function create(){
		if(!$this->is_validation()){
        	$data['list'] = $this->get_categorys();
        
			$this->load->view('admini/header');
			$this->load->view('admini/sider');
			$this->load->view('admini/category/create',$data);
			$this->load->view('admini/footer');        	
        }else{
			$this->category_model->parentid = $this->input->post('parentid');
			$this->category_model->cat_name = $this->input->post('catname');
			$this->category_model->is_show = 0;
			if($this->input->post('isshow')){
				$this->category_model->is_show = 1;
			}

			$doc = 'temp';
			$updata = $this->common->upload_image($doc);
			if(!empty($updata['thumb']['file_name'])){
				$source = 'uploads/'.$doc.'/'.$updata['thumb']['file_name'];
				$newpath = 'uploads/icon_t/';
				$suffix = '_t';
				$raw_name = $updata['thumb']['raw_name'];
				$ext = $updata['thumb']['file_ext'];
				$this->common->thumb($source, $newpath, $suffix,$width = 50,$height = 50);
				$this->category_model->icon = 'uploads/icon_t/'.$raw_name.$suffix.$ext;
			}

			$result = $this->category_model->create();
			if($result > 0){
				echo '<script>alert("信息提交成功");location.href="'.base_url('admini/category/create').'";</script>';
			}
		}
	}
	
	/**
	 * 修改分类
	 */
	public function edit($cid){
		$this->category_model->cat_id = $cid;
		if(!$this->is_validation()){
        	$data['list'] = $this->get_categorys();
        	$data['category'] = $this->category_model->get_categorybyid();
        	
			$this->load->view('admini/header');
			$this->load->view('admini/sider');
			$this->load->view('admini/category/edit',$data);
			$this->load->view('admini/footer');        	
        }else{
			$this->category_model->parentid = $this->input->post('parentid');
			$this->category_model->cat_name = $this->input->post('catname');
			$this->category_model->is_show = 0;
			if($this->input->post('isshow')){
				$this->category_model->is_show = 1;
			}
			$doc = 'temp';
			$updata = $this->common->upload_image($doc);
			if(!empty($updata['thumb']['file_name'])){
				$source = 'uploads/'.$doc.'/'.$updata['thumb']['file_name'];
				$newpath = 'uploads/icon_t/';
				$suffix = '_t';
				$raw_name = $updata['thumb']['raw_name'];
				$ext = $updata['thumb']['file_ext'];
				$this->common->thumb($source, $newpath, $suffix,$width = 50,$height = 50);
				$this->category_model->icon = 'uploads/icon_t/'.$raw_name.$suffix.$ext;
			}
			$result = $this->category_model->update();
			if($result > 0){
				echo '<script>alert("信息提交成功");location.href="'.base_url('admini/category/listing').'";</script>';
			}	
		}
	}

	/**
	 * 删除分类
	 */
	public function delete($cid){
		$this->load->model('goods_model');
		$this->goods_model->cat_id = $cid;
		$total = $this->goods_model->get_total();
		if($total > 0){
			echo '<script>alert("该分类下有商品，不能删除");location.href="'.base_url('admini/category/listing').'";</script>';
		}else{
			$this->category_model->cat_id = $cid;
			$row = $this->category_model->get_categorybyid();
			if(is_file($row['icon'])){
				@unlink($row['icon']);
			}
			$rs = $this->category_model->delete();
			if($rs){
				echo '<script>alert("信息提交成功");location.href="'.base_url('admini/category/listing').'";</script>';
			}
		}  	
	}

	/**
	 * 分类列表
	 */
	public function listing(){
		$data['list'] = $this->get_categorys();
		$this->load->view('admini/header');
		$this->load->view('admini/sider');
		$this->load->view('admini/category/list',$data);
		$this->load->view('admini/footer');        	
	}

	/**
	 * 获取全部分类
	 */
	private function get_categorys(){
		$catlist = $this->category_model->get_categorys();
		$this->load->library('common');
	    $result = common::get_subs($catlist,0);
	    return $result;	    
	}
	
	/**
	 * 输入验证
	 */
	private function is_validation(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('catname', '分类名称', 'required',
            array('required' => '必须输入分类名称')
        );
        return $this->form_validation->run();
	}	
}
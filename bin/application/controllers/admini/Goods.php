<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Goods extends Admin_Controller {
	public function __construct(){
        parent::__construct();  
        $this->load->model('goods_model');
        $this->load->library('common');
    }
    
	/**
	 * 获取全部分类
	 */
	private function get_categorys(){
		$this->load->model('category_model'); 
		$catlist = $this->category_model->get_categorys();
	    $result = common::get_subs($catlist,0);
	    return $result;	    
	}
	
	/**
	 * 获取商品列表
	 */
	public function listing($page = 1){
		$limit = 15;
		$offset = ($page-1)*$limit;
		$rows = intval($this->goods_model->get_total());
		$totalpage = ceil($rows/$limit);
		
		$this->load->library('pagination');
		$config['base_url'] = 'admini/goods/listing/';
		$config['total_rows'] = $rows;
		$config['per_page'] = $limit;
		$this->pagination->initialize($config);
	
		$list = $this->goods_model->get_goods($limit, $offset);
		$data['list'] = $list;
		$this->load->view('admini/header');
		$this->load->view('admini/sider');
        $this->load->view('admini/goods/list',$data);
		$this->load->view('admini/footer');
	}
			
	/**
	 * 添加商品
	 */
	public function create(){
	   if(!$this->is_validation()){
        	$data['categorys'] = $this->get_categorys();
        
			$this->load->view('admini/header');
			$this->load->view('admini/sider');
			$this->load->view('admini/goods/create',$data);
			$this->load->view('admini/footer');        	
        }else{
			$this->goods_model->cat_id = $this->input->post('catid');
			$this->goods_model->goods_name = $this->input->post('goods_name');
			$this->goods_model->price = $this->input->post('price');
			$this->goods_model->stock = $this->input->post('stock');
			$this->goods_model->weight = $this->input->post('weight');
			$this->goods_model->goods_detail = $this->input->post('goods_detail',FALSE);
			$attrs = $this->input->post('attrs');

			if($this->input->post('is_sales')){
				$this->goods_model->is_sales = 1;
			}else{
				$this->goods_model->is_sales = 0;
			};
			$this->goods_model->sort_order = $this->input->post('sort');

    		$doc = 'temp';
			$updata = $this->common->upload_image($doc);
			$piccount = count($updata);
			if(!empty($updata['thumb']['file_name'])){
				$source = 'uploads/'.$doc.'/'.$updata['thumb']['file_name'];
				$newpath = 'uploads/goods_t/';
				$suffix = '_t';
				$raw_name = $updata['thumb']['raw_name'];
				$ext = $updata['thumb']['file_ext'];
				$this->common->thumb($source, $newpath, $suffix,$width = 300,$height = 300);
				$this->goods_model->thumb = 'uploads/goods_t/'.$raw_name.$suffix.$ext;
				$piccount = $piccount - 1; 
			}
		
			$this->db->trans_start();
			$result = $this->goods_model->create();
			if($result > 0){
			    //添加商品图片	
				$pic_data = array(); 
				for($j = 0; $j < $piccount; $j++){
					$source = 'uploads/'.$doc.'/'.$updata[$j]['file_name'];
					$newpath = 'uploads/goods_t/';
					$suffix = '_t';
					$raw_name = $updata[$j]['raw_name'];
					$ext = $updata[$j]['file_ext'];
					$this->common->thumb($source, $newpath, $suffix,$width = 500,$height = 500);
					
					$picarry = array(
						'goods_id' => $result,
        				'picurl' => 'uploads/goods_t/'.$raw_name.$suffix.$ext
        				 );
					$pic_data[] = $picarry;
				}
				if(!empty($pic_data)){
					$this->load->model('pictures_model');
			    	$this->pictures_model->create($pic_data);
				}
				if(!empty($attrs)){
					$goods_attrs = array();
					foreach($attrs as $key => $val){
						if(!empty($val['attr_value'])){
							$goods_attrs[$key]['goods_id'] = $result;
							$goods_attrs[$key]['attr_value'] = $val['attr_value'];
							$goods_attrs[$key]['attr_price'] = $val['attr_price'];
							$goods_attrs[$key]['attr_weight'] = $val['attr_weight'];
						}	
					}
					if(!empty($goods_attrs)){
						$this->load->model('goods_attr_model');
			    		$this->goods_attr_model->create($goods_attrs);
					}
				}
			    $this->db->trans_complete();
				echo '<script>alert("信息提交成功");location.href="'.base_url('admini/goods/create').'";</script>';
			}
		}
	}
	
	/**
	 * 编辑商品
	 * @ return integer
	 */
	public function edit($id){
		 if(!$this->is_validation()){
			$data = array();
        	$data['categorys'] = $this->get_categorys();
            
			$this->goods_model->id = $id;
			$data['goods'] = $this->goods_model->get_goodsbyid();
			
			$this->load->model('pictures_model');
			$this->pictures_model->goods_id = $id;
			$data['pictures'] = $this->pictures_model->get_picturesbygid();

			$this->load->model('goods_attr_model');
			$this->goods_attr_model->goods_id = $id;
			$data['attrs'] = $this->goods_attr_model->get_attrsbygid();
				
			$this->load->view('admini/header');
			$this->load->view('admini/sider');
			$this->load->view('admini/goods/edit',$data);
			$this->load->view('admini/footer');        	
        }else{
			$this->load->model('pictures_model');

			$this->goods_model->id = $id;
			$this->goods_model->cat_id = $this->input->post('catid');
			$this->goods_model->goods_name = $this->input->post('goods_name');
			$this->goods_model->price = $this->input->post('price');
			$this->goods_model->stock = $this->input->post('stock');
			$this->goods_model->weight = $this->input->post('weight');
			$this->goods_model->goods_detail = $this->input->post('goods_detail',FALSE);
			if($this->input->post('is_sales')){
				$this->goods_model->is_sales = 1;
			}else{
				$this->goods_model->is_sales = 0;
			};
			$this->goods_model->sort_order = $this->input->post('sort');
			$attrs = $this->input->post('attrs');

    		$doc = 'temp';
			$updata = $this->common->upload_image($doc);
			$piccount = count($updata);
			if(!empty($updata['thumb']['file_name'])){
				$source = 'uploads/'.$doc.'/'.$updata['thumb']['file_name'];
				$newpath = 'uploads/goods_t/';
				$suffix = '_t';
				$raw_name = $updata['thumb']['raw_name'];
				$ext = $updata['thumb']['file_ext'];
				$this->common->thumb($source, $newpath, $suffix,$width = 300,$height = 300);
				$this->goods_model->thumb = 'uploads/goods_t/'.$raw_name.$suffix.$ext;
				$piccount = $piccount - 1; 
			}
		
			$this->db->trans_begin();
			$result = $this->goods_model->update();
			
		    //商品图片	
			$pic_data = array(); 
			for($j=0; $j<$piccount; $j++){
				$source = 'uploads/'.$doc.'/'.$updata[$j]['file_name'];
				$newpath = 'uploads/goods_t/';
				$suffix = '_t';
				$raw_name = $updata[$j]['raw_name'];
				$ext = $updata[$j]['file_ext'];
				$this->common->thumb($source, $newpath, $suffix,$width = 500,$height = 500);
				
				$picarry = array(
					'goods_id' => $id,
					'picurl' => 'uploads/goods_t/'.$raw_name.$suffix.$ext
				);
				$pic_data[] = $picarry;
			}
			if(!empty($pic_data)){
		    	$this->pictures_model->create($pic_data);
			}

			$this->load->model('goods_attr_model');
			$this->goods_attr_model->deletebygid($id);
			if(!empty($attrs)){
				$goods_attrs = array();
				foreach($attrs as $key => $val){
					if(!empty($val['attr_value'])){
						$goods_attrs[$key]['goods_id'] = $id;
						$goods_attrs[$key]['attr_value'] = $val['attr_value'];
						$goods_attrs[$key]['attr_price'] = $val['attr_price'];
						$goods_attrs[$key]['attr_weight'] = $val['attr_weight'];
					}	
				}
				if(!empty($goods_attrs)){
					$this->goods_attr_model->create($goods_attrs);
				}
			}
		    if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
		        echo '<script>alert("信息提交失败");</script>';
			}else{
			    $this->db->trans_commit();
				echo '<script>alert("信息提交成功");location.href="'.base_url('admini/goods/listing').'";</script>';
			}
		}
	}
	
	/**
	 * 根据id删除商品图片
	 * @param pid 图片id
	 * @return json
	 */
	public function delete_picture(){
		$pid = $this->input->post('pid');
		$this->load->model('pictures_model');
		$this->pictures_model->id = $pid;
		$pic = $this->pictures_model->get_picturebyid();
		if(is_file($pic['picurl'])){
			@unlink($pic['picurl']);
		}
		$result = $this->pictures_model->deletebyid();
		if($result > 0){
			$data['code'] = 200;
			$data['datas'] = array('msg' => '信息提交成功');	
		}else{
			$data['code'] = 400;
			$data['datas'] = array('msg' => '信息提交失败');	
		}
		echo json_encode($data);
	}	
	
	/**
	 * 删除商品
	 */
	public function delete($gid){
		$this->load->model('pictures_model');
		$this->pictures_model->goods_id = $gid;
		$pictures = $this->pictures_model->get_picturesbygid();
		foreach($pictures as $pic){
			if(is_file($pic['picurl'])){
				@unlink($pic['picurl']);
			}
		}
		$this->goods_model->id = $gid;
		$goods = $this->goods_model->get_goodsbyid();
		if(is_file($goods['thumb'])){
			@unlink($goods['thumb']);
		}
		
		$this->db->trans_start();
		$this->pictures_model->delete();
		$result = $this->goods_model->delete();
		if($result > 0){
			$this->db->trans_complete();
			redirect(base_url('admini/goods/listing'));
		}
	}
	
	/**
	 * 商品上下架
	 */
	public function set_status($id, $status){
		if($status == 0){
			$status = 1;
		}else{
			$status = 0;
		}
		$result = $this->goods_model->set_status($id, $status);
		if($result > 0){
			redirect(base_url('admini/goods/listing'));
		}
	}

	/**
	 * 输入验证
	 */
	private function is_validation(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('goods_name', '商品名称', 'required',
            array('required' => '必须输入商品名称')
        );
        return $this->form_validation->run();
	}	
}
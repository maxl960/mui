<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Goods extends CI_Controller {
	public function __construct(){
        parent::__construct();
        $this->load->model('goods_model');
    }
    
	/**
	 * 根据id获取商品的详细信息
	 * @param goodsid 商品id
	 * @return json
	 */
	public function get_goodsbyid(){
		$gid = $this->input->post('goodsid');
		if(empty($gid)){
			$msg = array('code' => 400, 'datas'=> array('error' => '没有数据'));	
			echo json_encode($msg);
		}else{
			$this->goods_model->id = $gid;
			$goods = $this->goods_model->get_goodsbyid();
			$this->load->model('pictures_model');
			$this->pictures_model->goods_id = $gid;
			$pictures = $this->pictures_model->get_picturesbygid();
			$this->load->model('goods_attr_model');
			$this->goods_attr_model->goods_id = $gid;
			$attrs = $this->goods_attr_model->get_attrsbygid();
			$data = ['code' => 200 ,'datas' => ['goods' => $goods, 'pictures' => $pictures, 'attrs' => $attrs]];
			echo json_encode($data);
		}
	}
	
	/**
	 * 根据分类获取商品列表
	 * @param catid 分类id
	 * @param page 当前页
	 * @param sort 排序方式 id,sort_order
	 * @param order ASC=升序 DESC = 降序
	 * @return json
	 */
	public function get_goodslist(){
		$catid = empty($_POST['catid']) ? 0 : $_POST['catid'];
		$page = empty($_POST['page']) ? 1 : $_POST['page'];
		$sort = empty($_POST['sort']) ? 'id' : $_POST['sort'];
	    $order = empty($_POST['order']) ? 'DESC' : $_POST['order'];

        $limit = 15;
        $offset = ($page-1)*$limit;
		$this->goods_model->cat_id = $catid;
		$this->goods_model->is_sales = 1;
		$rows = intval($this->goods_model->get_total());
		$totalpage = ceil($rows/$limit);
		$list = $this->goods_model->get_goods($limit, $offset, $sort, $order);
		
		$arr = array();
		foreach ($list as $key => $val) {
			$arr[$key]['id'] = $val['id'];
			$arr[$key]['cat_name'] = $val['cat_name'];
			$arr[$key]['goods_sn'] = $val['goods_sn'];	
			$arr[$key]['goods_name'] = $val['goods_name'];	
			$arr[$key]['price'] = $val['price'];	
			$arr[$key]['stock'] = $val['stock'];
			$arr[$key]['weight'] = $val['weight'];
			$arr[$key]['thumb'] = $val['thumb'];			
		}
    	$data['code'] = 200;
		$data['pages'] = $totalpage;
		$data['datas'] = array('goodslist' => $arr);	
		echo json_encode($data);
	}

	/**
    *获取商品的评价信息
	*@param page 当前页
	*@param goods_sn 商品编号
	*@return json
    */
	public function get_commentlist(){
		$goods_sn = $this->input->post('goods_sn');
		if(!empty($goods_sn)){
			$this->load->model('comment_model');
			$page = isset($_POST['page']) ? $_POST['page'] : 1;

			$limit = 15;
			$offset = ($page-1)*$limit;
			$this->comment_model->goods_sn = $goods_sn;
			$this->comment_model->is_show = 1;
			$rows = intval($this->comment_model->get_total());
			$totalpage = ceil($rows/$limit);
			$list = $this->comment_model->get_commentlist($limit, $offset);
			
			$arr = array();
			foreach ($list as $key => $val) {
				$arr[$key]['id'] = $val['id'];
				$arr[$key]['content'] = $val['content'];
				$arr[$key]['rank'] = $val['rank'];	
				$arr[$key]['reply'] = $val['reply'];
				$arr[$key]['add_time'] = $val['add_time'];			
			}
			$data['code'] = 200;
			$data['pages'] = $totalpage;
			$data['datas'] = array('commentlist' => $arr);	
			echo json_encode($data);
        }else{
			$msg = array('code' => 400, 'datas'=> array('error' => '数据请求错误'));	
			echo json_encode($msg);
		}
	}

	/**
	*商品搜索
	*@param goods_name
	*@return json
	*/
	public function search(){
		$goods_name = $this->input->post('goods_name',TRUE);
		if(empty($goods_name)){
			$data = array('code' => 400, 'datas'=> array('error' => '商品名称不能为空'));
		}else{
			$query = $this->goods_model->find_goods($goods_name);
			$data = array('code' => 200, 'datas'=> array('goodslist' => $query));
		}
		echo json_encode($data);
	}
}
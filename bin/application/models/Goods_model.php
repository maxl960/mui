<?php
class Goods_model extends CI_Model {
	
    public $id;              //商品id
    public $goods_sn;        //商品编码
    public $cat_id;          //分类id
    public $goods_name;      //商品名称
    public $price;           //单价
	public $stock;           //库存
	public $weight;          //重量
	public $thumb;           //缩略图
	public $goods_detail;    //商品详情
	public $add_time;        //添加时间
    public $last_update;     //更新时间 
	public $is_sales;        //销售状态
	public $sort_order;      //排序

    /**
     * 添加商品
     * @return integer
     */
    public function create(){
		$data = array(
	    	'cat_id' => $this->cat_id,
	        'goods_name' => $this->goods_name,
	        'price' => $this->price,
	        'stock' => $this->stock,
	        'weight' => $this->weight,
	        'thumb' => $this->thumb,
	        'goods_detail' => $this->goods_detail,
			'add_time' => time(),
	        'last_update' => time(),
	        'is_sales' => $this->is_sales,
	        'sort_order' => $this->sort_order
		);
		$this->db->insert('goods', $data);
        $goosid = $this->db->insert_id();
		if($goosid > 0){
			$goods_sn =  10000 + $goosid;
			$this->db->set('goods_sn', 'G'.$goods_sn);
        	$this->db->where('id', $goosid);
       		$this->db->update('goods');		
		}
		return $goosid;
    } 
    
    /**
     * 修改商品
     * @return integer
     */
    public function update(){
		if(!empty($this->thumb)){
			$this->db->set('thumb', $this->thumb);
		}
		$this->db->set('cat_id', $this->cat_id);
		$this->db->set('goods_name', $this->goods_name);
		$this->db->set('price', $this->price);
		$this->db->set('stock', $this->stock);
		$this->db->set('weight', $this->weight);
		$this->db->set('goods_detail', $this->goods_detail);
		$this->db->set('last_update', time());
		$this->db->set('is_sales', $this->is_sales); 
		$this->db->set('sort_order', $this->sort_order);
        $this->db->where('id', $this->id);
        $this->db->update('goods');			
		return $this->db->affected_rows();
	}
	
	/**
     * 修改商品库存
     * @return integer
     */
    public function update_stock($num, $goods_sn){
		$this->db->set('stock', 'stock-'.$num, FALSE);
        $this->db->where('goods_sn', $goods_sn);
        $this->db->update('goods');	
	
		return $this->db->affected_rows();
	}

    /**
     * 删除商品
     * @return integer
     */
    public function delete(){
		$this->db->where('id', $this->id);
		$this->db->delete('goods');
		return $this->db->affected_rows();
	}
    
	/**
	 * 获取商品列表
	 * @param $limit  记录数 
	 * @param $offset 偏移
	 * @param $this->cat_id 商品类别 0全部
	 * @return array
	 */
	public function get_goods($limit, $offset, $sort = 'id', $order = 'DESC'){
		//print_r($this->db);
		$this->load->database();
		if($this->cat_id > 0){
			$this->db->where('cat_id', $this->cat_id);
			$this->db->or_where('parentid', $this->cat_id);
		}
		if($this->is_sales > 0){
			$this->db->where('is_sales', $this->is_sales);
		}
		$this->db->select('g.id id,cat_id,goods_sn,goods_name,price,stock,weight,thumb,add_time,last_update,is_sales,sort_order,cat_name');
	    $this->db->from('goods g');
		$this->db->join('category c', 'g.cat_id = c.id','left');
		switch($sort){
			case 'sort_order':
			  	$this->db->order_by('sort_order',$order);
			  	break;
			default:
			  	$this->db->order_by('id',$order);
				break;
		}
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		return $query->result_array();
	}

	//SELECT * FROM `t_goods` WHERE (carry_mode = 2 OR (carry_mode = 1 AND userid IN (SELECT userid FROM `t_shipping` WHERE carry_mode = 1 AND FIND_IN_SET(?, region) GROUP BY userid))) AND isauth = 1 AND is_sales = 1 ";
			
	/**
	 * 根据id获取商品信息
	 * @param $this->id 商品id
	 * @return array
	 */
	public function get_goodsbyid(){
		$this->db->where('id', $this->id);
		$query = $this->db->get('goods');
		return $query->row_array(0);
	}
	
	/**
	 * 根据商品编码获取商品信息
	 * @param $this->goods_sn 商品编码
	 * @return array
	 */
	public function get_goodsbysn(){
		$this->db->where('goods_sn', $this->goods_sn);
		$query = $this->db->get('goods');
		return $query->row_array(0);
	}

	/**
	* 获取商品总数(商品管理)
	* @param $this->cat_id 商品类别 0全部
	* @return integer
	*/
	public function get_total(){
		if($this->cat_id > 0){
			$this->db->where('cat_id', $this->cat_id);
		}
		if($this->is_sales > 0){
			$this->db->where('is_sales', $this->is_sales);
		}
		$query = $this->db->get('goods');
		return $query->num_rows();
	}

	/**
	 * 商品上架下架
	 * @param $id 商品ID
	 * @param $status 状态0=下架,1=上架
	 * @return integer
	 */
	public function set_status($id, $status){
		$this->db->set('is_sales', $status);
        $this->db->where('id', $id);
        $this->db->update('goods');
				
		return $this->db->affected_rows();
	}

	/**
	*商品查询
	*@param $goods_name
	*@return array 
	*/
	public function find_goods($goods_name){
 		$strsql = "SELECT id,goods_sn sn,goods_name,price,stock,weight,thumb,0 as goods_type FROM t_goods WHERE goods_name LIKE '%".$goods_name."%' AND is_sales=1 UNION SELECT id,act_sn sn,act_name goods_name,act_price price,act_stock stock,act_weight weight,act_thumb thumb,act_type as goods_type FROM t_activity WHERE act_name LIKE '%".$goods_name."%' AND is_effective=1 AND is_finished=0";
		 
		$query = $this->db->query($strsql);
		return $query->result_array();
	}
}
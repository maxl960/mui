<?php
class Activity_model extends CI_Model {
	
    public $id;              //id
    public $act_sn;          //活动编码
    public $act_name;        //活动名称
    public $act_type;        //活动类型
    public $goods_id;        //商品id
    public $act_thumb;        //促销图片
    public $start_time;      //开始时间
	public $end_time;        //结束时间
    public $act_price;           //单价
	public $act_stock;           //库存
	public $act_weight;          //重量
	public $group_number;     //团购人数
    public $rebate_number;    //返利人数
    public $rebate_rate;      //返利比例
    public $rebate_cycle;     //返利周期
	public $frequency;        //返利次数
    public $is_finished;      //是否结束
    public $is_effective;      //是否有效

    /**
     *添加活动
     *@return integer
     */
    public function create(){
		$data = array(
	    	'act_name' => $this->act_name,
	        'act_type' => $this->act_type,
	        'goods_id' => $this->goods_id,
            'act_thumb' => $this->act_thumb,
	        'start_time' => $this->start_time,
	        'end_time' => $this->end_time,
	        'act_price' => $this->act_price,
	        'act_stock' => $this->act_stock,
			'act_weight' => $this->act_weight,
	        'group_number' => $this->group_number,
	        'rebate_number' => $this->rebate_number,
            'rebate_rate' => $this->rebate_rate,
            'rebate_cycle' => $this->rebate_cycle,
			'frequency' => $this->frequency,
	        'is_finished' => 0,
            'is_effective' => 1
		);
		$this->db->insert('activity', $data);
        $act_id = $this->db->insert_id();
		if($act_id > 0){
			$act_sn =  10000 + $act_id;
			$this->db->set('act_sn', 'P'.$act_sn);
        	$this->db->where('id', $act_id);
       		$this->db->update('activity');		
		}
		return $act_id;
    } 
    
    /**
     *修改活动
     *@return integer
     */
    public function update(){
        if(!empty($this->act_thumb)){
			$this->db->set('act_thumb', $this->act_thumb);
		}
		$this->db->set('act_name', $this->act_name);
		$this->db->set('start_time', $this->start_time);
		$this->db->set('end_time', $this->end_time);
		$this->db->set('act_price', $this->act_price);
		$this->db->set('act_stock', $this->act_stock);
		$this->db->set('act_weight', $this->act_weight);
		$this->db->set('group_number', $this->group_number);
		$this->db->set('rebate_number', $this->rebate_number); 
		$this->db->set('rebate_rate', $this->rebate_rate);
        $this->db->set('rebate_cycle', $this->rebate_cycle); 
		$this->db->set('rebate_rate', $this->rebate_rate);
		$this->db->set('frequency', $this->frequency);
        $this->db->where('id', $this->id);
        $this->db->update('activity');			
		return $this->db->affected_rows();
	}
	
    /**
     *删除活动
     *@return integer
     */
    public function delete(){
		$this->db->where('id', $this->id);
		$this->db->delete('activity');
		return $this->db->affected_rows();
	}
    
	/**
	 *获取活动列表
	 * @param $limit  记录数 
	 * @param $offset 偏移
	 * @param $this->act_type 类别 0全部
	 * return object
	 */
	public function get_activitylist($limit, $offset, $sort = 'id', $order = 'DESC'){
		$this->db->select('*,IF(is_finished=1 or is_effective=0,0,1) is_sales');
		if($this->act_type > 0){
			$this->db->where('act_type', $this->act_type);
		}
		if($this->is_effective > 0){
			$this->db->where('is_effective', $this->is_effective);
		}
		switch($sort){
			case 'start_time':
			  $this->db->order_by('start_time',$order);
			  break;
			case 'end_time':
			  $this->db->order_by('end_time',$order);
			  break;
			default:
			  $this->db->order_by('id',$order);
			  break;
		}
		$this->db->limit($limit, $offset);
		$query = $this->db->get('activity');
		return $query->result_array();
	}

	/**
	 *根据id获取活动信息
	 * @param $this->id 商品id
	 * return object
	 */
	public function get_activitybyid(){
		$this->db->where('id', $this->id);
		$query = $this->db->get('activity');
		return $query->row_array(0);
	}
	
	/**
	 * 根据商品编码获取商品信息
	 * @param $this->goods_sn 商品编码
	 * @return array
	 */
	public function get_activitybysn(){
		$this->db->where('act_sn', $this->act_sn);
		$query = $this->db->get('activity');
		return $query->row_array(0);
	}
    
	/**
	 *根据id获取活动信息
	 * @param $this->id 商品id
	 * return object
	 */
	public function get_activityinfo(){
		$this->db->select('a.id id,act_sn goods_sn,goods_name,goods_detail,act_name,goods_id,act_thumb,start_time,end_time,act_price,act_stock,act_weight,group_number,rebate_number,rebate_rate,rebate_cycle,rebate_rate,frequency,IF(is_finished=1 or is_effective=0,0,1) is_sales');
	    $this->db->from('activity a');
		$this->db->join('goods g', 'g.id = a.goods_id','inner');
		$this->db->where('a.id', $this->id);
		$query = $this->db->get();
		return $query->row_array(0);	
	}
	
	/**
	 * 获取活动商品总数(活动管理)
	 * @param $this->act_type 类别
	 * return integer
	 */
	public function get_total(){
		if($this->act_type > 0){
			$this->db->where('act_type', $this->act_type);
		}
		if($this->is_effective > 0){
			$this->db->where('is_effective', $this->is_effective);
		}
		$query = $this->db->get('activity');
		return $query->num_rows();
	}

	/**
	 * 设置活动是否有效
	 * @param $id 
	 * @param $status 状态 0 = 无效,1 = 有效
	 * return integer
	 */
	public function set_status($id, $status){
		$this->db->set('is_effective', $status);
        $this->db->where('id', $id);
        $this->db->update('activity');
				
		return $this->db->affected_rows();
	}

	/**
     * 修改活动库存
     * @return integer
     */
    public function update_stock($num, $act_sn){
		$this->db->set('act_stock','act_stock-'.$num, FALSE);
        $this->db->where('act_sn', $act_sn);
        $this->db->update('activity');			
		return $this->db->affected_rows();
	}
}
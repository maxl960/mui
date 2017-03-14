<?php
class Shipping_model extends CI_Model {
    public $id;                //id
    public $carry_name;        //配送名称
    public $carry_mode;        //配送方式
    public $region;            //配送区域
    public $first_weight;      //首重 
	public $first_price;       //首费
	public $second_weight;     //续重
	public $second_price;      //续费
	public $free_amount;       //包邮金额

    /**
     * 添加运费模板
     * @return integer
     */
    public function create(){
		$data = array(
	    	'carry_name' => $this->carry_name,
	        'carry_mode' => $this->carry_mode,
	        'region' => $this->region,
	        'first_weight' => $this->first_weight,
	        'first_price' => $this->first_price,
	        'second_weight' => $this->second_weight,
	        'second_price' => $this->second_price,
	        'free_amount' => $this->free_amount
		);
		$this->db->insert('shipping',$data);
        return $this->db->insert_id();
    } 
    
    /**
     * 修改运费模板
     * @return integer
     */
    function update(){
		$data = array(
	    	'carry_name' => $this->carry_name,
	        'carry_mode' => $this->carry_mode,
	        'region' => $this->region,
	        'first_weight' => $this->first_weight,
	        'first_price' => $this->first_price,
	        'second_weight' => $this->second_weight,
	        'second_price' => $this->second_price,
	        'free_amount' => $this->free_amount
		);
        $this->db->where('id', $this->id);
        $this->db->update('shipping',$data);			
		return $this->db->affected_rows();
	}
	
    /**
     * 删除运费模板
     * @return integer
     */
    public function delete(){
		$this->db->where('id', $this->id);
		$this->db->delete('shipping');
		return $this->db->affected_rows();
	}

	/**
	 * 获取运费模板列表
	 * @return array
	 */
	public function get_shippinglist(){
        $query = $this->db->get('shipping');
        return $query->result_array();
	}

	/**
	 * 获取运费模板列表
	 * @return array
	 */
	public function get_editregoin(){
		$this->db->where('id <>', $this->id);
        $query = $this->db->get('shipping');
        return $query->result_array();
	}

	/**
	 * 根据id获取运费模板信息
	 * @param $this->id
	 * @return array
	 */
	public function get_shippingbyid(){
		$this->db->where('id', $this->id);
		$query = $this->db->get('shipping');
		return $query->row_array(0);
	}
    
	/**
	 * 获取某地区的运费模板
	 * @param $this->region
	 * @return array
	 */
	public function get_shippingbyregion(){
		$strsql = "SELECT * FROM t_shipping WHERE FIND_IN_SET(?, region) ORDER BY id DESC";
		$query = $this->db->query($strsql, $this->region);
		return $query->row_array(0);
	}
}
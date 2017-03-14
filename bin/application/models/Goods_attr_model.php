<?php
class Goods_attr_model extends CI_Model {
    /**
    *添加商品属性
    *@return integer
    */
    public function create($data){
		$this->db->insert_batch('goods_attr', $data);
        return $this->db->affected_rows();
    }

    /**
    *根据商品id获取商品属性
    *@return array
    */
    public function get_attrsbygid(){
    	$this->db->where('goods_id', $this->goods_id);
    	$query = $this->db->get('goods_attr');
		return $query->result_array();
	}
    
    /**
    *根据id获取商品属性
    *@return array
    */
    public function get_attrbyid(){
    	$this->db->where('id', $this->id);
    	$query = $this->db->get('goods_attr');
		return $query->row_array(0);
	}

    /**
     * 根据商品id删除商品属性
     * @return integer
     */
    public function deletebygid($gid){
		$this->db->where('goods_id', $gid);
		$this->db->delete('goods_attr');
		return $this->db->affected_rows();
	}  
}

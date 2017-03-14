<?php
class Pictures_model extends CI_Model {
	
    public $id;          //ID
    public $goods_id;    //商品id
    public $picurl;      //图片地址

    /**
     * 添加商品图片
     * @return integer
     */
    public function create($data){
		$this->db->insert_batch('pictures', $data);
        return $this->db->affected_rows();
    } 
    
    /**
     * 根据商品id删除商品图片
     * @return integer
     */
    public function delete(){
		$this->db->where('goods_id', $this->goods_id);
		$this->db->delete('pictures');
		return $this->db->affected_rows();
	}
	
	/**
     * 根据图片id删除商品图片
     * @return integer
     */
    public function deletebyid(){
		$this->db->where('id', $this->id);
		$this->db->delete('pictures');
		return $this->db->affected_rows();
	}
	
	/**
     * 根据商品id获取图片
     * @return integer
     */
    public function get_picturesbygid(){
    	$this->db->where('goods_id', $this->goods_id);
    	$query = $this->db->get('pictures');
		return $query->result_array();
	}
	
	/**
     * 根据图片id获取图片信息
     * @return integer
     */
    public function get_picturebyid(){
    	$this->db->where('id', $this->id);
    	$query = $this->db->get('pictures');
		return $query->row_array(0);
	}
}
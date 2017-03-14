<?php
class Car_model extends CI_Model {

    /**
     *添加购物车
     *@return integer
     */
    public function create($data){
		$this->db->insert('car', $data);
        $goosid = $this->db->insert_id();
		if($goosid > 0){
			return $goosid;		
		}else{
			return 0;
		}	
    } 
    
    /**
     *修改购物车数量
     *@return integer
     */
    public function edit_num($data,$member_id){
		$this->db->where(array('product_sn'=>$data['product_sn'],'custom_id'=>$member_id,'attr_id'=>$data['attr_id']));
		$this->db->set('num',$data['num']);
		$this->db->update('car');
		return $this->db->affected_rows();
	}
	
    /**
    *删除购物车商品
    *@return integer
    */
    public function delet($sn,$member_id,$attr_id){
		$this->db->where(array('product_sn'=>$sn,'attr_id'=>$attr_id,'custom_id'=>$member_id));
		$this->db->delete('car');
		return $this->db->affected_rows();
	}
    
	public function deletebysn($goods_sn,$member_id){
		$this->db->where_in('product_sn', $goods_sn, FALSE);
		$this->db->where('custom_id',$member_id);
		$this->db->delete('car');
		return $this->db->affected_rows();
	}
}
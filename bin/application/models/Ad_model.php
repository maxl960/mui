<?php
class Ad_model extends CI_Model {
   
	public function get_ads($order = 'DESC'){
		$this->db->where('enabled',1)->order_by('id',$order)->limit(5);
		$query = $this->db->get('advertisement');
		return $query->result_array();
	}
			
}
<?php
class Area_model extends CI_Model{
	public $area_code;
	public $area_name;
	public $parent_code;
	
	/**
	 * 根据上级id获取地区信息
	 * @param  $this->parent_code
	 * @return array 
	 */
	public function get_areabyparent(){
		$this->db->where('parent_code', $this->parent_code); 
		$query = $this->db->get('area');
		return $query->result_array();
	}
	
	/**
	 * 获取街区信息
	 * @param  $this->parent_code
	 * @return array 
	 */
	public function get_streetlist(){
		$sql = "SELECT * FROM `t_area` WHERE left(parent_code,4) = ? ORDER BY left(area_code,6), area_code";
		$query = $this->db->query($sql, $this->parent_code);
		return $query->result_array();
	}
	
}
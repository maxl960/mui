<?php
class Category_model extends CI_Model {
    
    public $cat_id;      //分类id
    public $cat_name;    //分类名称
    public $parentid;    //父id
    public $icon;        //图标
    public $is_show;     //是否显示 
    /**
     *添加分类
     *@return integer
     */
    public function create(){
		$data = array(
	    	'cat_name' => $this->cat_name,
	        'parentid' => $this->parentid,
            'icon' => $this->icon,
	        'is_show' => $this->is_show
		);
		$this->db->insert('category', $data);
        return $this->db->insert_id();
    }
    
    /**
     *修改分类
     *@return integer
     */
    public function update(){
		$data = array(
	    	'cat_name' => $this->cat_name,
	        'parentid' => $this->parentid,
            'icon' => $this->icon,
	        'is_show' => $this->is_show
		);
	    $this->db->where('id', $this->cat_id);
        $this->db->update('category', $data);
        return $this->db->affected_rows();
    }
    
    /**
     *删除分类
     *@return integer
     */
    public function delete(){
	    $this->db->where('id', $this->cat_id);
        $this->db->delete('category');
        return $this->db->affected_rows();
    }

    /**
     *获取全部分类信息
     * @return array
     */
    public function get_categorys(){
        $this->load->database();
        
    	if($this->is_show == 1){
			$this->db->where('is_show', 1); 
		}	
        $query = $this->db->get('category');
        return $query->result_array();
    }
    
    /**
     *根据id获取分类信息
     * @param cid
     * @return object
     */
    public function get_categorybyid(){
        $this->db->where('id', $this->cat_id); 
        $query = $this->db->get('category');
        return $query->row_array(0);
    }

    /**
     *获取子分类信息
     * @param cid
     * @return object
     */
    public function get_categorysbyparent(){
    	if($this->is_show == 1){
			$this->db->where('is_show', 1); 
		}		
        $this->db->where('parentid', $this->cat_id); 
        $query = $this->db->get('category');
        return $query->result_array();
    }

    /**
     *获取同级分类信息
     * @param parent_id
     * @return object
     */
    public function get_siblingsbyparent(){
    	if($this->is_show == 1){
			$this->db->where('is_show', 1); 
		}	
        $this->db->where('parentid', $this->parentid); 
        $query = $this->db->get('category');
        return $query->result_array();
    }
}
<?php
class Dictionary_model extends CI_Model {

    /**
	 * 获取字典列表
	 * @param $val 
	 * @return array
	 */
	public function get_dictionarybyvalue($val){
        $this->db->from('dictionarydata a');
        $this->db->join('dictionary b', 'a.dict_value = b.dict_value','left');
		$this->db->where_in('a.dict_value', $val, FALSE);
		$query = $this->db->get();
		$list = $query->result_array();

        foreach ($list as $key => $value) {
            $array[$value['dict_name']][$value['dictdata_value']] = $value['dictdata_name'];
        }
        return $array;
	}

}
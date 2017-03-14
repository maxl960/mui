<?php
class Log_model extends CI_Model {
	
    public $id; //id
    public $operation_type; //操作类型
    public $order_sn; //订单编号
    public $content; //操作内容
    public $amount; //金额
    public $member_id; //会员id
    public $operator; //操作人
    public $create_time; //操作时间

    /**
    *添加日志
    *@return integer
    */
    public function create(){
		$data = array(
	    	'operation_type' => $this->operation_type,
	        'order_sn' => $this->order_sn,
	        'content' => $this->content,
	        'amount' => $this->amount,
	        'member_id' => $this->member_id,
	        'operator' => $this->operator,
	        'create_time' => time()
		);
		$this->db->insert('t_operation_log', $data);
        return $this->db->insert_id();
    }

    /**
	*获取日志列表
	*@return array
	*/
	public function get_log_list($limit, $offset){
		$this->db->select('o.*,m1.telephone tel1,m2.telephone tel2');
	    $this->db->from('t_operation_log o');
		$this->db->join('member m1', 'o.member_id = m1.id','left');
		$this->db->join('member m2', 'o.operator = m2.id','left');
        $this->db->where('operation_type', $this->operation_type);
        $this->db->limit($limit, $offset);
		$query = $this->db->get();
		return $query->result_array();
	}

    /**
	*获取日志总数
	*@return integer
	*/
	public function get_total(){
		$this->db->where('operation_type', $this->operation_type);
		$query = $this->db->get('t_operation_log');
		return $query->num_rows();
	} 
}
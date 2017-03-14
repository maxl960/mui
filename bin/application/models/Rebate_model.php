<?php
class Rebate_model extends CI_Model {
	
    public $id;
    public $member_id; //会员id
    public $order_sn; //订单号码
    public $amount; //返利总额
    public $previous_rebate; //返利金额
    public $last_rebate; //最后返利金额
    public $rebate_cycle; //返利周期
    public $frequency; //返利次数
    public $returned; //已返还次数
    public $start_time; //返利开始时间
    public $end_time; //返利结束时间
    public $operator; //操作人

    /**
    *添加返利
    *@return integer
    */
    public function create(){
		$data = array(
	    	'member_id' => $this->member_id,
            'order_sn'=> $this->order_sn,
            'amount'=> $this->amount,
            'previous_rebate'=> $this->previous_rebate,
            'last_rebate' => $this->last_rebate,
            'rebate_cycle' => $this->rebate_cycle,
            'frequency' => $this->frequency,
            'returned' => $this->returned,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'operator' => $this->operator
		);
		$this->db->insert('rebate', $data);
        return $this->db->insert_id();
    }

    /**
    *添加返利批量
    *@return integer
    */
    public function create_batch($data){
		$this->db->insert_batch('rebate', $data);
        return $this->db->affected_rows();
    }  
}
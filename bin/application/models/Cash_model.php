<?php
class Cash_model extends CI_Model {
	
	public $id;
	public $amount;
	public $member_id;
	public $create_time;
	public $finish_time;
	public $status;

    /**
    *提现申请
    *@return integer
    */
    public function application_create(){
		$data = array(
            'amount'=> $this->amount,
            'member_id' => $this->member_id,
            'create_time' => time(),
            'status' => 0
		);
		$this->db->insert('cash_application', $data);
        return $this->db->insert_id();
    }
    
	/**
	*获取提现申请列表
	*@param $limit  记录数 
	*@param $offset 起始位置
	*@return array
	*/
	public function get_application_list($limit, $offset){
		if($this->member_id > 0){
			$this->db->where('member_id', $this->member_id);
		}
		$this->db->select('c.*,telephone,m.amounts totals,payment_account');
	    $this->db->from('cash_application c');
		$this->db->join('member m', 'c.member_id = m.id','inner');
		$this->db->order_by('id','DESC');
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	*获取提现申请总数
	*@return integer
	*/
	public function get_application_total(){
		if($this->member_id > 0){
			$this->db->where('member_id', $this->member_id);
		}
		$query = $this->db->get('cash_application');
		return $query->num_rows();
	}

	/**
	*设置提现申请状态
	*@return integer
	*/
	public function set_application_status(){
		$this->db->set('status', $this->status);
		$this->db->set('finish_time', time());
        $this->db->where('id', $this->id);
        $this->db->update('cash_application');
		return $this->db->affected_rows();
	}

	/**
	*修改用户支付密码
	*@param $pwd  支付密码 
	*@return integer
	*/
    public function set_payment_password($pwd){
		$this->db->set('payment_pwd', $pwd);
        $this->db->where('id', $this->member_id);
        $this->db->update('member');
		return $this->db->affected_rows();
	}

	/**
	*修改支付账户
	*@param $account 用户账号 
	*@return integer
	*/
	public function set_payment_account($account){
		$this->db->set('payment_account', $account);
        $this->db->where('id', $this->member_id);
        $this->db->update('member');
		return $this->db->affected_rows();
	}

	/**
	*根据用户id获取支付密码
	*@return string 
	*/
	public function get_payment_pwdbyid(){
		$this->db->select('payment_pwd');
		$this->db->where('id', $this->member_id);
		$query = $this->db->get('member');
		return $query->row_array();
	}
}
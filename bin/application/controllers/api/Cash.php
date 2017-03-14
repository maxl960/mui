<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cash extends Member_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('cash_model');
		$this->load->model('order_model');
		$this->load->model('user_model');
		$this->load->model('log_model');
    }
    
    /**
	*提现申请
    *@param password 支付密码
	*@param amount 提现金额
	*@return json 
	*/
	public function application(){
        $this->cash_model->member_id = $this->member_id;
    	$amount = floatval($this->input->post('amount'));
        $password = $this->input->post('password');
        if(empty($password) || $amount <= 0){
            $msg = array('code' => 400, 'datas'=> array('error' => '金额或者密码错误'));
        }else{
            $user = $this->user_model->get_userinfo($this->member_id); 
            if($user['amounts'] < $amount){
                $msg = array('code' => 400, 'datas'=> array('error' => '账户余额不足'));
            }else{
                $this->cash_model->member_id = $this->member_id;
                $this->cash_model->amount = $amount;
                $row = $this->cash_model->get_payment_pwdbyid();
                if(password_verify($password, $row['payment_pwd'])){
                    if($this->cash_model->application_create()){
                        $msg = array('code' => 200, 'datas'=> array('msg' => '信息提交成功'));	
                    }
                }else{
                    $msg = array('code' => 400, 'datas'=> array('error' => '支付密码错误'));
                }        
            }
        }
        echo json_encode($msg);	
    }

    /**
	*修改用户支付密码
	*@param old_password 旧密码
    *@param new_password 新密码  
	*@return json
	*/
    public function set_payment_password(){
        $old_password = $this->input->post('old_password');
        $new_password = $this->input->post('new_password');
        
        if(empty($new_password)){
            $msg = array('code' => 400, 'datas'=> array('error' => '密码不能为空'));
        }else{
            $this->cash_model->member_id = $this->member_id;
            $row = $this->cash_model->get_payment_pwdbyid();
            if(empty($row['payment_pwd'])){
                if($this->cash_model->set_payment_password(password_hash($new_password, PASSWORD_BCRYPT))){
                    $msg = array('code' => 200, 'datas'=> array('msg' => '操作成功'));
                }
            }else{
                if(password_verify($old_password, $row['payment_pwd'])){
                    if($this->cash_model->set_payment_password(password_hash($new_password, PASSWORD_BCRYPT))){
                        $msg = array('code' => 200, 'datas'=> array('msg' => '操作成功'));
                    }
                }else{
                    $msg = array('code' => 400, 'datas'=> array('error' => '旧密码错误'));
                }
            }
        }	
        echo json_encode($msg);
	}

    /**
	*修改用户支付账户
	*@param password 支付密码
    *@param payment_account 支付账号   
	*@return json
	*/
    public function set_payment_account(){
        $password = $this->input->post('password');
        $payment_account = $this->input->post('payment_account');
        
        if(empty($password) || empty($payment_account)){
            $msg = array('code' => 400, 'datas'=> array('error' => '账号或者密码不能为空'));
        }else{
            $this->cash_model->member_id = $this->member_id;
            $row = $this->cash_model->get_payment_pwdbyid();
            if(password_verify($password, $row['payment_pwd'])){
                if($this->cash_model->set_payment_account($payment_account)){
                    $msg = array('code' => 200, 'datas'=> array('msg' => '操作成功'));
                }
            }else{
                $msg = array('code' => 400, 'datas'=> array('error' => '支付密码错误'));
            }    
        }	
        echo json_encode($msg);
	}

    /**
	*账户余额支付
	*@param password 支付密码
	*@param amount 支付金额
	*@param order_sn 订单编号
    *@return json 
	*/
	public function balance_pay(){
		$password = $this->input->post('password');
        if(empty($password)){
            $msg = array('code' => 400, 'datas'=> array('error' => '支付密码不能为空'));
        }else{
			$amount = $this->input->post('amount');
			if($amount <= 0){
				$msg = array('code' => 400, 'datas'=> array('error' => '支付金额错误'));
				echo json_encode($msg);
				die;
			} 
			$order_sn = $this->input->post('order_sn');
			if(empty($order_sn)){
				$msg = array('code' => 400, 'datas'=> array('error' => '订单号不能为空'));
				echo json_encode($msg);
				die;
			}
			$this->order_model->order_sn = $order_sn;
			$order = $this->order_model->get_orderbysn();
            if($order['pay_status'] == 1){
				$msg = array('code' => 400, 'datas'=> array('error' => '该订单已付款'));
				echo json_encode($msg);
				die;
			}
            $member_id = $this->member_id;
			$user = $this->user_model->get_userinfo($member_id); 
            if($user['amounts'] < $amount){
				$msg = array('code' => 400, 'datas'=> array('error' => '账户余额不足'));
				echo json_encode($msg);
				die;
			} 
            $this->cash_model->member_id = $member_id;
            $row = $this->cash_model->get_payment_pwdbyid();
            if(password_verify($password, $row['payment_pwd'])){
                $this->db->trans_begin();
				$this->order_model->pay_way = 1;	
				$this->order_model->set_pay_status();
                
				$this->user_model->member_id = $member_id;
				$this->user_model->amount_minus($amount);
				
				$this->log_model->operation_type = 6;
				$this->log_model->content = '购买商品余额支付'.$amount.'元';
				$this->log_model->amount = $amount;
				$this->log_model->order_sn = $order_sn;
				$this->log_model->member_id = $member_id;
				$this->log_model->operator = $member_id;
				$this->log_model->create();

				if ($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$msg = array('code' => 400, 'datas'=> array('error' => '操作失败'));
				}else{
					$this->db->trans_commit();
					$msg = array('code' => 200, 'datas'=> array('msg' => '操作成功'));
				}
            }else{
                $msg = array('code' => 400, 'datas'=> array('error' => '支付密码错误'));
            }    
        }	
        echo json_encode($msg);
	}

    /**
    *查询账户余额
    *@return json
    */
    public function get_member_amount(){
        $row = $this->user_model->get_userinfo($this->member_id);
        if(empty($row)){
            $msg = array('code' => 400, 'datas'=> array('error' => '查询失败'));
        }else{
            $msg = array('code' => 200, 'datas'=> array('amount' => $row['amounts']));
        } 
        echo json_encode($msg);
    }
}
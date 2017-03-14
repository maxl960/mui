<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Controller{
	public function __construct(){
        parent::__construct();
        $this->config->load('alipay', TRUE);
		$this->load->model('order_model');
		$this->load->model('log_model');
    }
    
    /**
	*支付宝支付信息
	*@param $method
	*@return
	*/
	public function alipay_info(){
		header('Content-type: text/plain');
		require_once(APPPATH.'third_party/alipay/alipay_rsa.function.php');
		require_once(APPPATH.'third_party/alipay/alipay_core.function.php');

        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = $this->input->post('order_sn');
        //订单名称，必填
        $subject = '伴买伴送'.$out_trade_no;
	    
		$this->order_model->order_sn = $out_trade_no;
		$order = $this->order_model->get_orderbysn();
        //付款金额，必填
        $total_fee = $order['goods_amount'] + $order['shipping_fee'];
        //商品描述，必填
        $body = $this->input->post('goods_desc');
        
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"partner"       => $this->config->item('partner', 'alipay'),
				"seller_id"  => $this->config->item('seller_id', 'alipay'),
				"out_trade_no"	=> $out_trade_no,
				"subject"	=> $subject,
				'body'           => $body,
				"total_fee"	=> $total_fee,
				'notify_url' => $this->config->item('notify_url', 'alipay'),
				"service"       => $this->config->item('service', 'alipay'),
				"payment_type"	=> $this->config->item('payment_type', 'alipay'),
				"_input_charset"	=> trim(strtolower($this->config->item('input_charset', 'alipay')))
		);	

		//生成需要签名的订单
		$data = createLinkstring($parameter);
		$rsa_sign = urlencode(rsaSign($data, $this->config->item('private_key', 'alipay')));
		//把签名得到的sign和签名类型sign_type拼接在待签名字符串后面。
		$data = $data.'&sign="'.$rsa_sign.'"&sign_type="RSA"';
		//返回给客户端,建议在客户端使用私钥对应的公钥做一次验签，保证不是他人传输。
		echo $data;
	}
	
	/**
	*支付宝回调接口
	*@param $method 
	* @return
	*/
	public function alipay_notify(){
		// 加载支付宝返回通知类库
		require_once(APPPATH."third_party/alipay/alipay_notify.class.php");
		// 初始化支付宝返回通知类
		$alipayNotify = new AlipayNotify($this->config->item('alipay'));
		$isalipay =  $alipayNotify->getResponse($_POST['notify_id']);
		if($isalipay == "true"){
			$verify_result = $alipayNotify->getSignVeryfy($_POST, $_POST['sign']);
			if($verify_result){
				if ($_POST['trade_status'] == 'TRADE_SUCCESS') {	
					$this->save_pay_info($_POST['out_trade_no'],$_POST['total_fee'],1);
				}
				echo "success";		//请不要修改或删除
			}
			else {
				echo "fail";	
			}
		}
	}
    
	/**
	*微信支付信息
	*@param $method
	*@return
	*/
	public function wxpay_info(){
		header('Content-type: text/plain');
		require_once(APPPATH.'third_party/wxpay/WxPay.Api.php');
		require_once(APPPATH.'third_party/wxpay/WxPay.Data.php');

		// 订单号，示例代码使用时间值作为唯一的订单ID号
		$out_trade_no = $this->input->post('order_sn');  
		$this->order_model->order_sn = $out_trade_no;
		$order = $this->order_model->get_orderbysn();
        //付款金额，必填
        $total = ($order['goods_amount'] + $order['shipping_fee'])*100;
       	//商品名称
		$subject = $this->input->post('goods_desc');

		$unifiedOrder = new WxPayUnifiedOrder();
		$unifiedOrder->SetBody($subject);//商品或支付单简要描述
		$unifiedOrder->SetOut_trade_no($out_trade_no.'-'.time());
		$unifiedOrder->SetTotal_fee($total);
		$unifiedOrder->SetTrade_type("APP");
		$result = WxPayApi::unifiedOrder($unifiedOrder);
		if (is_array($result)) {
			echo json_encode($result);
		}
	}

	/**
	*微信支付回调接口
	*@param $method
	*@return
	*/
	public function wxpay_notify(){
		require_once(APPPATH.'third_party/wxpay/WxPay.Exception.php');
        require_once(APPPATH.'third_party/wxpay/WxPay.Data.php');
		//获取通知的数据
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		//如果返回成功则验证签名
		try {
			$result = WxPayResults::Init($xml);
			if(is_array($result)){
				$rs = explode("-",$result['out_trade_no']);
				$this->save_pay_info($rs[0], $result['total_fee']/100, 2);
			}
			echo 'SUCCESS';
		} catch (WxPayException $e){
			$msg = $e->errorMessage();
			return false;
		}
	}
    
	/**
	*保存支付信息
	*@param $order_sn 订单编号
	*@param $amount 支付金额
	*@param $payway 支付方式
	*@return boolean
	*/
	private function save_pay_info($order_sn, $amount, $payway){
		switch ($payway){
			case 1:
				$payval = "支付宝支付";
				break;
			case 2:
				$payval = "微信支付";
				break;
			default:
				$payval = "";
				break;
		}
		$this->order_model->order_sn = $order_sn;
		$order = $this->order_model->get_orderbysn();
		$this->db->trans_begin();
		$this->order_model->pay_way = $payway;	
		if($this->order_model->set_pay_status() > 0){
			$this->log_model->operation_type = 6;
			$this->log_model->content = '购买商品使用'.$payval.$amount.'元';
			$this->log_model->amount = $amount;
			$this->log_model->order_sn = $order_sn;
			$this->log_model->member_id = $order['member_id'];
			$this->log_model->operator = 0;
			$this->log_model->create();
			$this->db->trans_complete();
			return true;
		}else{
			$this->db->trans_rollback();
			return false;
		}
	}
}
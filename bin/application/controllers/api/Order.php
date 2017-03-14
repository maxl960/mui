<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends Member_Controller {
	public function __construct(){
        parent::__construct();
        $this->load->model('order_model');
		$this->load->model('shipping_model');
    }
    
	/**
	*获取购物车中商品的详细信息
	*@return json
	*/
	public function get_cart(){
		$list = $this->order_model->get_cartsfromdb($this->member_id);
		if(empty($list)){
			$msg = array('code' => 400, 'datas'=> array('error' => '购物车中没有商品'));	
			echo json_encode($msg);
		}else{
			$data = array('code' => 200,'datas' => array('goodslist' => $list));
			echo json_encode($data);
		}
	}

	/**
	*生成购物清单
	*@param cart     购物车信息
	*@param province 所在省份
	*@return json
	*/
	public function get_order_info(){
        $cart = $this->input->post('cart');
        $province = $this->input->post('province');

		if(empty($cart)){
			$msg = array('code' => 400, 'datas'=> array('error' => '购物车中没有商品'));	
			echo json_encode($msg);
			die;
		}
		if(empty($province)){
			$msg = array('code' => 400, 'datas'=> array('error' => '请选择收货地址'));	
			echo json_encode($msg);
			die;
		}else{
			$this->shipping_model->region = $province;
			$shipping = $this->shipping_model->get_shippingbyregion();
			if(empty($shipping)){
				$msg = array('code' => 400, 'datas'=> array('error' => '所在地区不在销售范围'));	
				echo json_encode($msg);
				die;
			}
		}
		
		$arry = json_decode($cart);
        $goods_amount = 0;
        $shipping_fee = 0;
		$goodsarr = array();
		$weight = 0;
		foreach($arry as $item) {
			$arr1 = array(); 
			if(isset($item->attrid)){
				$attrid = $item->attrid;
			}else{
				$attrid = 0;
			}
			$val = $this->order_model->get_orderdetailinfo($item->sn, $attrid);
			if($val['is_sales']){
				$amount = floatval($val['price']) * floatval($item->num);
				$goods_amount = $goods_amount + $amount;
                $weight = $weight + floatval($val['weight']) * floatval($item->num);

				$arr1['goods_sn'] = $val['sn'];
				$arr1['goods_name'] = $val['gname'];
				$arr1['price'] = $val['price'];
				$arr1['number'] = $item->num;
				$arr1['goods_type'] = $val['goods_type'];
				$arr1['amount'] =  $amount * floatval($val['rebate_rate']) / 100;
				$arr1['rebate_cycle'] = $val['rebate_cycle'];
				$arr1['frequency'] = $val['frequency'];
				$goodsarr[] = $arr1;
			}
		}
		if($goods_amount >= $shipping['free_amount'] && $shipping['free_amount'] > 0){
			$shipping_fee = 0;
		}else{
			if($weight < $shipping['first_weight']){
				$shipping_fee = $shipping['first_price'];
			}else{
				$first_price = $shipping['first_price'];
				$w1 = $weight - $shipping['first_weight'];
				$second_price = ceil($w1 / $shipping['second_weight']) * $shipping['second_price'];
				$shipping_fee = $first_price + $second_price;
			}
		}
		$data['code'] = 200;
		$data['datas'] = array('goodslist' => $goodsarr,'goods_amount'=>$goods_amount,'shipping_fee' =>$shipping_fee,'totals'=>$goods_amount + $shipping_fee);
		echo json_encode($data);
	}
    
	/**
	*购买商品
	*@return json
	*/
	public function buy(){
		$cart = $this->input->post('cart');
        $province = $this->input->post('province');
        
		if(empty($cart)){
			$msg = array('code' => 400, 'datas'=> array('error' => '购物车中没有商品'));	
			echo json_encode($msg);
			die;
		}
		if(empty($province)){
			$msg = array('code' => 400, 'datas'=> array('error' => '请选择收货地址'));	
			echo json_encode($msg);
			die;
		}else{
			$this->shipping_model->region = $province;
			$shipping = $this->shipping_model->get_shippingbyregion();
			if(empty($shipping)){
				$msg = array('code' => 400, 'datas'=> array('error' => '所在地区不在销售范围'));	
				echo json_encode($msg);
				die;
			}
		}
	
		$city = $this->input->post('city');
        $district = $this->input->post('district');
		$address = $this->input->post('address');
		$consignee = $this->input->post('consignee');
		$telephone = $this->input->post('telephone'); 
		$token = $this->input->post('token');
		$memo = $this->input->post('memo');
		$member_id = $this->member_id;
		$goods_sn = '';

        $arry = json_decode($cart);
        $goods_amount = 0;
        $shipping_fee = 0;
		$goodsarr = array();
		$weight = 0;
		foreach($arry as $item) {
			$arr1 = array(); 
			if(isset($item->attrid)){
				$attrid = $item->attrid;
			}else{
				$attrid = 0;
			}
			$val = $this->order_model->get_orderdetailinfo($item->sn, $attrid);
			if($val['is_sales']){
				$amount = floatval($val['price']) * floatval($item->num);
				$goods_amount = $goods_amount + $amount;
                $weight = $weight + floatval($val['weight']) * floatval($item->num);

				$arr1['goods_sn'] = $val['sn'];
				if(empty($val['attr_value'])){
					$arr1['goods_name'] = $val['gname'];
				}else{
					$arr1['goods_name'] = $val['gname'].'-'.$val['attr_value'];
				}
				$arr1['price'] = $val['price'];
				$arr1['number'] = $item->num;
				$arr1['goods_type'] = $val['goods_type'];
				$arr1['rebate_amount'] =  $amount * floatval($val['rebate_rate']) / 100;
				$arr1['group_number'] = $val['group_number'];
				$arr1['rebate_number'] = $val['rebate_number'];
				$arr1['rebate_cycle'] = $val['rebate_cycle'];
				$arr1['frequency'] = $val['frequency'];
				$goodsarr[] = $arr1;
				$goods_sn = $goods_sn.",'".$val['sn']."'";
			}
		}
		if($goods_amount >= $shipping['free_amount'] && $shipping['free_amount'] > 0){
			$shipping_fee = 0;
		}else{
			if($weight < $shipping['first_weight']){
				$shipping_fee = $shipping['first_price'];
			}else{
				$first_price = $shipping['first_price'];
				$w1 = $weight - $shipping['first_weight'];
				$second_price = ceil($w1 / $shipping['second_weight']) * $shipping['second_price'];
				$shipping_fee = $first_price + $second_price;
			}
		}
        
		$this->order_model->member_id = $member_id;
		$this->order_model->consignee = $consignee;
		$this->order_model->telephone = $telephone;
		$this->order_model->province = $province; 
		$this->order_model->city = $city; 
        $this->order_model->district = $district;
		$this->order_model->address = $address; 
		$this->order_model->memo = $memo;
        $this->order_model->goods_amount = $goods_amount;
        $this->order_model->shipping_fee = $shipping_fee;
		 
		$this->db->trans_start();
		$order_sn = $this->order_model->create_order();
		if(!empty($order_sn)){
			foreach ($goodsarr as $key => $val) {
				$goodsarr[$key]['order_sn'] = $order_sn;
				$goodsarr[$key]['grouping'] = 0;
				$goodsarr[$key]['goods_status'] = 0;
                
				if($val['goods_type'] == 0){
					$this->load->model('goods_model');
					$this->goods_model->update_stock($val['number'],$val['goods_sn']);
				}else{
					$this->load->model('activity_model');
					$this->activity_model->update_stock($val['number'],$val['goods_sn']);
				}
				if($val['goods_type'] == 2){
					$groupinfo = $this->order_model->get_lastgroupingbysn($val['goods_sn']);
					if(empty($groupinfo)){
						$goodsarr[$key]['grouping'] = $val['group_number'].'-1-1';
					}else{
						$t = explode("-", $groupinfo['grouping']);
						if(intval($t[0]) > intval($t[1])){
							$goodsarr[$key]['grouping'] = $t[0].'-'.(intval($t[1]) + 1).'-'.$t[2];
						}else{
							$goodsarr[$key]['grouping'] = $t[0].'-1-'.(intval($t[2]) + 1);
						}
					}
				}
			}
			$result = $this->order_model->create_order_goods($goodsarr);
			if($result > 0){
				$goods_sn = substr($goods_sn, 1);
				$this->load->model('car_model');
				$this->car_model->deletebysn($goods_sn,$this->member_id);
				$this->db->trans_complete();
		        $msg = array('code' => 200, 'datas'=> array('order_sn' => $order_sn));	
				echo json_encode($msg);
			}else{
				$msg = array('code' => 400, 'datas'=> array('error' => '数据提交错误'));	
				echo json_encode($msg);
			}
		}
	}

	/**
	*取得购物车中的商品信息
	*/
	private function get_goodslist($cart){
		$goods_sn = "";
        $act_sn = "";
        $num = array();
		$attrs = array();
        $arry = json_decode($cart);

		foreach($arry as $item) { 
			switch (substr($item->sn,0,1)){
				case 'G':
					$goods_sn = $goods_sn.",'".$item->sn."'";
					break;
				case 'P':
					$act_sn = $act_sn.",'".$item->sn."'";
					break;
			}
			if(isset($item->num)){
				$num[$item->sn] = $item->num;
			}
			if(isset($item->attrid)){
				$attrs[$item->sn][] = $item->attrid;
			}
		}
		
		if(!empty($goods_sn)){
			$goods_sn = substr($goods_sn, 1);
		}
		if(!empty($act_sn)){
			$act_sn = substr($act_sn, 1);
		}
		$data['goods'] = $this->order_model->get_carts($goods_sn, $act_sn);
		$data['num'] = $num;
		$data['attrs'] = $attrs;
		return $data; 
	}

	/*$province = '辽宁省';
		$arr4 = array(  
			array("sn"=>"G10010", "num"=>'2'),
			array("sn"=>"G10011", "num"=>'2'),  
			array("sn"=>"G10012", "num"=>'3'),  
			array("sn"=>"P10001", "num"=>'5'),  
			array("sn"=>"P10002", "num"=>'6'),  
			array("sn"=>"P10003", "num"=>'10'),  
			array("sn"=>"P10005", "num"=>'1')  
		);  
		$cart = json_encode($arr4);

		$province = '辽宁省';
		$arr4 = array(  
			array("sn"=>"G10001", "num"=>'1',"attrid"=>22),
			array("sn"=>"G10001", "num"=>'1',"attrid"=>24),
			array("sn"=>"G10002", "num"=>'1'),
			array("sn"=>"P10001", "num"=>'2')
		);  
		$cart = json_encode($arr4);*/
		
}
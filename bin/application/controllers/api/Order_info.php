<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_info extends Member_Controller {
    public function __construct() {
        parent::__construct();
		$this->load->model('order_model');
        $this->load->model('dictionary_model');
    }

    /**
	*获取会员订单列表
    *@param $order_status 订单状态 100=全部,1=待付款,2=待发货,3=已发货,4=已成交,-1=已取消,-2=已退货,-3已退款,-4=退货申请,-5退款申请
    *@param order_sn 订单编号
    *@return json
	*/
    public function get_order_list(){
        $order_status = empty($_POST['order_status']) ? 100 : $_POST['order_status'];
		$page = empty($_POST['page']) ? 1 : $_POST['page'];
        switch ($order_status){
            case 1:
                $this->order_model->order_status = 0;
                $this->order_model->pay_status = 0;
                break;
            case 2:
                $this->order_model->order_status = 0;
                $this->order_model->pay_status = 1;
                break;
            case 3:
                $this->order_model->order_status = 1;
                $this->order_model->pay_status = 100;
                break;
            case 4:
                $this->order_model->order_status = 2;
                $this->order_model->pay_status = 100;
                break;                  
            default:
                $this->order_model->order_status = $order_status;
                $this->order_model->pay_status = 100;
                break;        
        }
        $this->order_model->member_id = $this->member_id;
        $limit = 15;
		$offset = ($page-1)*$limit;
		$rows = intval($this->order_model->get_ordertotal());
		$totalpage = ceil($rows/$limit);
		
		$query = $this->order_model->get_orderlist($limit, $offset);
        if(empty($query)){
            $list = null;
        }else{
            $list = $this-> set_orderinfo($query);
        }
        
        $data['code'] = 200;
		$data['pages'] = $totalpage;
		$data['datas'] = array('orderlist' => $list['orderlist'],'goodslist' => $list['goodslist']);	
		echo json_encode($data);
    }
    
    /**
    *取消订单
    *@param order_sn 订单编号
    *@return json
    */
    public function order_cancel(){              
        $order_sn = $this->input->post('order_sn');
        if(empty($order_sn)){
            $msg = array('code' => 400, 'datas'=> array('error' => '数据请求错误'));	
            echo json_encode($msg);
        }else{
            $this->order_model->reason = '买家主动取消';
            $this->order_model->operator = '买家';
            $this->order_model->member_id = $this->member_id;
            $this->order_model->order_sn = $order_sn;

            $this->db->trans_start();
            $result = $this->order_model->set_order_status('-1');
            if($result > 0){
                if($this->order_model->set_goods_status('-1')){
                    $this->order_model->set_finish_time();
                    $this->db->trans_complete();
                    $msg = array('code' => 200, 'datas'=> array('msg' => '操作成功'));	
                    echo json_encode($msg);
                }else{
                    $msg = array('code' => 400, 'datas'=> array('error' => '不能取消订单'));	
                    echo json_encode($msg);
                }
            }else{
                $msg = array('code' => 400, 'datas'=> array('error' => '不能取消订单'));	
                echo json_encode($msg);
            }
        }
    }
    
    /**
    *收货确认
    *@param order_sn 订单编号
    *@return json
    */
    public function receiving(){
        $order_sn = $this->input->post('order_sn');
        if(empty($order_sn)){
            $msg = array('code' => 400, 'datas'=> array('error' => '数据请求错误'));	
            echo json_encode($msg);
        }else{
            $this->order_model->member_id = $this->member_id;
            $this->order_model->order_sn = $order_sn;
            $this->db->trans_start();
            $result = $this->order_model->receiving();
            if($result > 0){
                if($this->goods_receiving($order_sn)){
                    $this->order_model->set_finish_time();
                    $this->db->trans_complete();
                    $msg = array('code' => 200, 'datas'=> array('msg' => '操作成功'));	
                    echo json_encode($msg);
                }else{
                    $msg = array('code' => 400, 'datas'=> array('error' => '信息提交失败'));	
                    echo json_encode($msg);
                }
            }else{
                $msg = array('code' => 400, 'datas'=> array('error' => '信息提交失败'));	
                echo json_encode($msg);
            }
        }
    }

    /**
    *退货退款
    *@param goods_id 订单商品id
    *@param reason 退货退款理由
    *@param flag 标识 1=退货,2=退款
    *@return json
    */
    public function ask_for_refund(){
        if(!isset($_POST['goods_id'])){
            $msg = array('code' => 400, 'datas'=> array('error' => '数据请求错误'));	
            echo json_encode($msg);
            die;
        }
        $this->order_model->id = $this->input->post('goods_id');
        $row = $this->order_model->get_order_goodsbyid();
        if($row['goods_type'] == 0){
            $this->order_model->reason = $this->input->post('reason');
            if($this->input->post('flag') == 2){
                $this->order_model->order_status = -5; 
            }else{
                $this->order_model->order_status = -4; 
            }
            if($this->order_model->set_goods_statusbyid()){
                $msg = array('code' => 200, 'datas'=> array('msg' => '信息提交成功'));	
                echo json_encode($msg);
            }else{
                $msg = array('code' => 400, 'datas'=> array('error' => '信息提交失败'));	
                echo json_encode($msg);
            }
        }else{
            $msg = array('code' => 400, 'datas'=> array('error' => '活动商品不能退换'));	
            echo json_encode($msg);
        }
    } 

    /**
    *根据编号获取订单信息
    *@param order_sn 订单编号  
    *@return json
    */
    public function get_orderinfo(){
        $order_sn = $this->input->post('order_sn');
        if(empty($order_sn)){
            $msg = array('code' => 400, 'datas'=> array('error' => '数据请求错误'));	
            echo json_encode($msg);
        }else{
            $dict_val = "1,4,5";
            $dic_list = $this->dictionary_model->get_dictionarybyvalue($dict_val);

            $this->order_model->order_sn = $order_sn;
            $this->order_model->member_id = $this->member_id;
            $order = $this->order_model->get_orderbysn();
            $order['status_val'] = $dic_list['order_status'][$order['order_status']];
            $order['pay_way_val'] = $dic_list['pay_way'][$order['pay_way']];
            $order['pay_status_val'] = $dic_list['pay_status'][$order['pay_status']];
            $goodslist = $this->order_model->get_goods_list($order_sn);

            $data['code'] = 200;
            $data['datas'] = array('order' => $order,'goodslist'=>$goodslist);
            echo json_encode($data);
        }    
    } 

    /**
    *订单查询结果处理
    *@return array
    */
    private function set_orderinfo($list){
        $goodslist = array();
		$orderlist = array();
        $dict_val = "1,4,5";
        $dic_list = $this->dictionary_model->get_dictionarybyvalue($dict_val);
  
        $order_sn = '';
		foreach ($list as $key => $val) {
            $orderlist[$key] = $val;
            $order_sn = $order_sn.",'".$val['order_sn']."'";
            $orderlist[$key]['status_val'] = $dic_list['order_status'][$val['order_status']];
            $orderlist[$key]['pay_way_val'] = $dic_list['pay_way'][$val['pay_way']];
            $orderlist[$key]['pay_status_val'] = $dic_list['pay_status'][$val['pay_status']];
        }
        $order_sn = substr($order_sn, 1);
        $list = $this->order_model->get_goods_list($order_sn);
		foreach ($list as $item) {
            $goodslist[$item['order_sn']][] = $item;  
        }
        $data['goodslist'] = $goodslist;
        $data['orderlist'] = $orderlist;
        return $data;
    }

    /**
    *商品收货确认
    *@return boolean
    */
    private function goods_receiving($order_sn){
        $this->order_model->order_sn = $order_sn;
        $list = $this->order_model->get_order_goodslist();
       
        $rebate = array();
        $id = '';
        $group_id = '';
        $group_goods = array();
        $rs = TRUE;

        foreach ($list as $item) {
            if($item['goods_status'] < 3 && $item['goods_status'] > 0){
                switch($item['goods_type']){
                    case 1:
                        $rebate[] = $this->set_rebate($this->member_id,$order_sn,$item['rebate_amount'],$item['frequency'],$item['rebate_cycle']);
                        $id = $id.','.$item['id']; 
                        break;
                    case 2:
                        $group_id = $group_id.','.$item['id'];
                        $group_goods[] = array('id' => $item['id'], 'goods_sn' => $item['goods_sn']);
                        break;
                    default:
                        $id = $id.','.$item['id']; 
                        break;        
                }
            }
        }

        $group_id = substr($group_id, 1);
        if(!empty($group_id)){
            if($this->order_model->receiving_goods($group_id, 3) <= 0){
                $rs = FALSE;
            }
        }
        foreach($group_goods as $val){
            $query = $this->order_model->get_groupingbysn($val['id'], $val['goods_sn']);
            if(count($query) == $query[0]['group_number']){
                $random_keys = array_rand($query, $query[0]['rebate_number']);
                foreach($random_keys as $random){
                    if($query[$random]['goods_status'] == 3){
                        $rebate[] = $this->set_rebate($query[$random]['member_id'],$query[$random]['order_sn'],$query[$random]['rebate_amount'],$query[$random]['frequency'],$query[$random]['rebate_cycle']);
                    }
                }
                foreach($query as $item){
                    if($item['goods_status'] == 3){
                        $id = $id.','.$item['id']; 
                    } 
                }
            }
        }

        $id = substr($id, 1);
        if(!empty($id)){
            if($this->order_model->receiving_goods($id, 4) <= 0){
                $rs = FALSE;
            }
        }
        if(!empty($rebate)){
            $this->load->model('rebate_model');
            if($this->rebate_model->create_batch($rebate) <= 0){
                $rs = FALSE;
            }
        }
        return $rs;
    }

    /**
    *返利计算
    */
    private function set_rebate($member_id,$order_sn,$rebate_amount,$frequency,$rebate_cycle){
        $arr = array();
        $arr['member_id'] = $member_id;
        $arr['order_sn'] = $order_sn;
        $arr['amount'] = $rebate_amount;
        $arr['previous_rebate'] = floor($rebate_amount/$frequency);
        $arr['last_rebate'] = $rebate_amount - (($frequency - 1) * $arr['previous_rebate']);
        $arr['rebate_cycle'] = $rebate_cycle;
        $arr['frequency'] = $frequency;
        $arr['returned'] = 0;
        $arr['start_time'] = time();
        $arr['end_time'] = time() + 3600 * 24 * $frequency * $rebate_cycle;
        $arr['operator'] = '买家确认收货返利';
        return $arr;
    }
}
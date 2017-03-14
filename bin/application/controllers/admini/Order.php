<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends Admin_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('order_model');
        $this->load->model('dictionary_model');
    }

    /**
     * @param $status 订单状态 100=全部,1=待付款,2=待发货,3=已发货,4=已成交,-1=已取消,-2=已退货,-3已退款,-4=退货申请,-5退款申请
	 * 订单列表
	 */
    public function listing($status = 100, $page = 1){
        switch ($status){
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
                $this->order_model->order_status = $status;
                $this->order_model->pay_status = 100;
                break;    
        }

        $this->order_model->order_sn = $this->input->post('order_sn');
        $this->order_model->telephone = $this->input->post('telephone');

        $limit = 15;
		$offset = ($page-1)*$limit;
		$rows = intval($this->order_model->get_ordertotal());
		$totalpage = ceil($rows/$limit);
		
		$this->load->library('pagination');
		$config['base_url'] = 'admini/order/listing/'.$status;
		$config['first_url'] = 'admini/order/listing/'.$status.'/1';
		$config['total_rows'] = $rows;
		$config['per_page'] = $limit;
		$this->pagination->initialize($config);

		$query = $this->order_model->get_orderlist($limit, $offset);
        if(empty($query)){
            $list = null;
        }else{
            $list = $this-> set_orderinfo($query);
        }
        
        $data['goodslist'] = $list['goodslist'];
        $data['orderlist'] = $list['orderlist'];

        $this->order_model->order_status = 0;
        $this->order_model->pay_status = 0;
        $data['count1'] = $this->order_model->get_ordertotal();
        $this->order_model->pay_status = 1;
        $data['count2'] = $this->order_model->get_ordertotal();

        $this->load->view('admini/header');
		$this->load->view('admini/sider');
        $this->load->view('admini/order/list',$data);
		$this->load->view('admini/footer');
    }
    
    /**
     * @param $status -4=退货申请,-5=退款申请
	 * 退货退款申请
	 */
    public function refund_listing($status = -4){
        $data['list'] = $this->order_model->get_goodslistbystatus($status);
        switch($status){
            case -5:
                $title = '退款处理';
                break;
            default:
                $title = '退货处理';
                break;
        }
        $data['status_title'] = $title;
        $data['status'] = $status;
        $this->load->view('admini/header');
		$this->load->view('admini/sider');
        $this->load->view('admini/order/refund',$data);
		$this->load->view('admini/footer');
    }

    /**
    *售后处理结果
    */
    public function refund_result(){
        $member_id = $this->input->post('member_id_post');
        $refund_amount = $this->input->post('refund_amount');
        $result = $this->input->post('result');
        $order_sn = $this->input->post('order_sn_post');
        $goods_id = $this->input->post('goods_id_post');
        $solution = $this->input->post('solution');
        $status = $this->input->post('goods_status');

        $this->order_model->id = $goods_id;
        $this->order_model->solution = $solution;
        $this->order_model->refund_amount = $refund_amount;
        $this->order_model->order_status = $result;

        if($this->order_model->set_goods_statusbyid()){
            if($result < 0){
                $this->load->model('user_model');
                $this->user_model->member_id = $member_id;
                $this->user_model->amount_plus($refund_amount);

                $this->load->model('log_model');
                $this->log_model->operation_type = 2;
                $this->log_model->order_sn = $order_sn; 
                $this->log_model->content = '订单号'.$order_sn.'商品'.$goods_id.'售后退款'.$refund_amount.'元';
                $this->log_model->amount = $refund_amount;
                $this->log_model->member_id = $member_id;
                $this->log_model->operator = 0;
                $this->log_model->create();
            }
            $this->db->trans_complete();
            echo '<script>alert("信息提交成功");location.href="'.base_url('admini/order/refund_listing/'.$status).'";</script>';   
        }else{
            echo '<script>alert("信息提交失败");</script>';
        }
    }

    /**
    * 取消订单
    */
    public function cancel($order_sn,$status,$page){
        $this->load->library('form_validation');
		$this->form_validation->set_rules('reason', '取消原因', 'required');
        if($this->form_validation->run() == FALSE){
            $data['order_sn'] = $order_sn;
            $data['status'] = $status;
            $data['page'] = $page;
            
            $this->load->view('admini/header');
            $this->load->view('admini/sider');
            $this->load->view('admini/order/cancel',$data);
            $this->load->view('admini/footer');
        }else{
           	$this->order_model->order_sn = $order_sn; 
		    $order = $this->order_model->get_orderbysn();

            $reason = $this->input->post('reason');
            $pay_status = $this->input->post('pay_status');
            if(!empty($_POST['memo'])){
                $reason = $reason.'-'.$_POST['memo'];
            }
		    $this->order_model->reason = $reason;
            $this->order_model->operator = '管理员';
            $this->db->trans_start();
		    $result = $this->order_model->set_order_status('-1');
            if($result){
                if($this->order_model->set_goods_status('-1')){
                    if($order['pay_status'] > 0 ){
                        $this->load->model('user_model');
                        $this->user_model->member_id = $order['member_id'];
                        $this->user_model->amount_plus($order['goods_amount']);

                        $this->load->model('log_model');
                        $this->log_model->operation_type = 2;
                        $this->log_model->order_sn = $order['order_sn']; 
                        $this->log_model->content = '取消订单'.$order['order_sn'].',退款'.$order['goods_amount'].'元';
                        $this->log_model->amount = $order['goods_amount'];
                        $this->log_model->member_id = $order['member_id'];
                        $this->log_model->operator = 0;
                        $this->log_model->create();
                    }
                    $this->order_model->set_finish_time();
                    $this->db->trans_complete();
                    echo '<script>alert("信息提交成功");location.href="'.base_url('admini/order/listing/'.$status.'/'.$page).'";</script>';
                }else{
                    echo '<script>alert("信息提交失败");</script>';
                }
            }else{
                echo '<script>alert("信息提交失败");</script>';
            }
        }    
    }

    /**
    * 修改订单价格
    */
    public function change_price($order_sn,$status,$page){
        $is_submit = $this->input->post('is_submit');
        if(empty($is_submit)){
            $data['order_sn'] = $order_sn;
            $data['status'] = $status;
            $data['page'] = $page;

            $this->order_model->order_sn = $order_sn;
            $data['order'] = $this->order_model->get_orderbysn();
            
            $this->load->view('admini/header');
            $this->load->view('admini/sider');
            $this->load->view('admini/order/change_price',$data);
            $this->load->view('admini/footer');
        }else{
            $amount = $this->input->post('amount');
            $shipping_fee = $this->input->post('shipping_fee');
            $this->order_model->order_sn = $order_sn; 
            $this->order_model->goods_amount = $amount;
            $this->order_model->shipping_fee = $shipping_fee;
		    $this->order_model->reason = $this->input->post('memo');
            $this->order_model->operator = '管理员';
            $this->db->trans_start();
		    $result = $this->order_model->change_total_price();
            if($result){
                $this->load->model('log_model');
                $this->log_model->operation_type = 3;
	            $this->log_model->order_sn = $order_sn; 
	            $this->log_model->content = '修改订单'.$order_sn.'的总价为'.$amount.'元,运费为'.$shipping_fee.'元';
	            $this->log_model->amount = $amount + $shipping_fee;
	            $this->log_model->member_id = $this->input->post('member_id');
	            $this->log_model->operator = 0;
                if($this->log_model->create()){
                    $this->db->trans_complete();
                    echo '<script>alert("信息提交成功");location.href="'.base_url('admini/order/listing/'.$status.'/'.$page).'";</script>';
                }else{
                    echo '<script>alert("信息提交失败");</script>';
                }
            }else{
                echo '<script>alert("信息提交失败");</script>';
            }
        }
    } 
    
    /**
    *获取订单详细信息
    */
    public function show($order_sn){
        $data = $this->get_orderbysn($order_sn);
        $this->load->view('admini/header');
		$this->load->view('admini/sider');
        $this->load->view('admini/order/show',$data);
		$this->load->view('admini/footer');
    }

    /**
    *发货设置
    */
    public function delivery(){
        $order_sn = $this->input->post('order_no');
        $this->order_model->order_sn = $order_sn;
        $this->order_model->shipping_sn = $this->input->post('shipping_sn');
        $this->order_model->operator = '管理员';
        $this->db->trans_start();
        $result = $this->order_model->set_order_status('1');
        if($result){
            if($this->order_model->set_goods_status('1')){
                $this->db->trans_complete();
                echo '<script>alert("信息提交成功");location.href="'.base_url('admini/order/show/'.$order_sn).'";</script>';
            }else{
                echo '<script>alert("信息提交失败");location.href="'.base_url('admini/order/show/'.$order_sn).'";</script>';
            }
        }else{
            echo '<script>alert("信息提交失败");location.href="'.base_url('admini/order/show/'.$order_sn).'";</script>';
        }
    }  

    /**
    *查询结果处理
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
            $item['goods_status_val'] = $dic_list['order_status'][$item['goods_status']]; 
            $goodslist[$item['order_sn']][] = $item; 
        }
        $data['goodslist'] = $goodslist;
        $data['orderlist'] = $orderlist;
        return $data;
    }

    /**
    *根据编号获取订单信息
    */
    private function get_orderbysn($order_sn){
        $dict_val = "1,4,5";
        $dic_list = $this->dictionary_model->get_dictionarybyvalue($dict_val);

        $this->order_model->order_sn = $order_sn;
		$order = $this->order_model->get_orderbysn();
        $order['status_val'] = $dic_list['order_status'][$order['order_status']];
        $order['pay_way_val'] = $dic_list['pay_way'][$order['pay_way']];
        $order['pay_status_val'] = $dic_list['pay_status'][$order['pay_status']];

        $data['order'] = $order;
        $data['goodslist'] = $this->order_model->get_goods_list($order_sn);
        return $data;
    }
}
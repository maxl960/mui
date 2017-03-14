<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comment extends Member_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('comment_model');
    }
    
    /**
	*商品评价
	*@param goods_sn 商品编号
    *@param order_sn 订单编号
    *@param content 评价内容 
    *@param rank 评分
	*@return json 
	*/
	public function assess(){
        $this->comment_model->member_id = $this->member_id;
    	$this->comment_model->goods_sn = $this->input->post('goods_sn');
        $this->comment_model->order_sn = $this->input->post('order_sn');
        $this->comment_model->content = $this->input->post('content');
        $this->comment_model->rank = $this->input->post('rank');

        if($this->comment_model->create()){
            $msg = array('code' => 200, 'datas'=> array('msg' => '商品评价成功'));	
            echo json_encode($msg);
        }else{
            $msg = array('code' => 400, 'datas'=> array('error' => '提交失败'));	
            echo json_encode($msg);
        }
	}

    /**
    *获取某个订单商品的评价
    *@param order_sn 订单编号
    *@param goods_sn 商品编号
    *@return json
    */
    public function get_comment(){
        if(isset($_POST['order_sn']) && isset($_POST['goods_sn'])){
            $this->comment_model->order_sn = $_POST['order_sn'];
            $this->comment_model->goods_sn = $_POST['goods_sn'];
            $row = $this->comment_model->get_commentbysn();
            $data['code'] = 200;
            $data['datas'] = array('comment' => $row);
            echo json_encode($data);
        }else{
            $msg = array('code' => 400, 'datas'=> array('error' => '数据请求错误'));	
            echo json_encode($msg);
        }
    }
}
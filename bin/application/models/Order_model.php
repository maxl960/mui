<?php
class Order_model extends CI_Model {
    public $id;              //订单id
    public $order_sn;        //订单编码
    public $member_id;       //会员id
    public $order_status;    //订单状态
    public $pay_status;      //支付状态
    public $pay_way;         //支付金额
	public $goods_amount;    //总金额
	public $shipping_fee;    //运费
	public $consignee;       //收货人
	public $telephone;       //联系电话
	public $province;        //省份
    public $city;            //城市
    public $district;        //区域
	public $address;         //详细地址
	public $memo;            //备注
    public $add_time;        //添加时间
    public $finish_time;     //完成时间
    public $shipping_sn;     //运单号码
    public $pay_time;        //付款时间
    public $reason;          //订单取消原因
    public $operator;        //操作人
    public $solution;        //处理结果
    public $refund_amount;   //退款金额

    /**
     * 添加订单信息
     * @return integer
     */
    public function create_order(){
        $order_sn = $this->make_sn($this->member_id);
		$data = array(
	    	'order_sn' => $order_sn,
	        'member_id' => $this->member_id,
	        'order_status' => 0,
	        'pay_status' => 0,
	        'goods_amount' => $this->goods_amount,
	        'shipping_fee' => $this->shipping_fee,
	        'consignee' => $this->consignee,
            'telephone' => $this->telephone,
	        'province' => $this->province,
	        'city' => $this->city,
            'district' => $this->district,
            'address' => $this->address,
	        'memo' => $this->memo,
			'add_time' => time()
		);
		$this->db->insert('order_info', $data);
        if($this->db->insert_id() > 0 ){
            return $order_sn;
        }else{
            return null;
        }
    }

    /**
     * 添加订单商品信息
     * @return integer
     */
    public function create_order_goods($data){
		$this->db->insert_batch('order_goods', $data);
        return $this->db->affected_rows();
    } 
    
    /**
    *修改支付状态
    *@return integer
    */
    public function set_pay_status(){
        $this->db->set('pay_status', 1);
        $this->db->set('pay_way', $this->pay_way);
        $this->db->set('pay_time', time());
        $this->db->where('order_sn', $this->order_sn);
        $this->db->update('order_info');			
		return $this->db->affected_rows();
    }

    /**
    *收货确认
    *@return integer
    */
    public function receiving(){
        $this->db->set('order_status',  2);
        $this->db->set('operator',  '买家');
        $this->db->where('member_id', $this->member_id);
        $this->db->where('order_sn', $this->order_sn);
        $this->db->update('order_info');			
		return $this->db->affected_rows();
    }

    /**
    *商品收货确认
    *@return integer
    */
    public function receiving_goods($id, $goods_status){
        $this->db->set('goods_status',  $goods_status);
        $this->db->where_in('id', $id , FALSE);
        $this->db->update('order_goods');			
		return $this->db->affected_rows();
    }    
    
    /**
    *设置订单完成时间
    *@return integer
    */
    public function set_finish_time(){
        $this->db->set('finish_time',  time());
        $this->db->where('order_sn', $this->order_sn);
        $this->db->update('order_info');			
		return $this->db->affected_rows();
    }

    /**
     * 修改订单状态
     * @return integer
     */
    public function set_order_status($status){
        if($status == 1){
            $this->db->set('shipping_sn',  $this->shipping_sn);
            $this->db->set('delivery_time',  time());
        }
        $this->db->set('order_status',  $status);
        $this->db->set('reason',  $this->reason);
        $this->db->set('operator',  $this->operator);
        $this->db->where('order_sn', $this->order_sn);
        if(!empty($this->member_id)){
            $this->db->where('member_id', $this->member_id);
            $this->db->where('order_status', 0);
		}
        $this->db->update('order_info');			
		return $this->db->affected_rows();
    }

    /**
     * 修改商品总价
     * @return integer
     */
    public function change_amount(){
        $this->db->set('goods_amount', $this->goods_amount);
        $this->db->set('reason',  $this->reason);
        $this->db->set('operator',  $this->operator);
        $this->db->where('order_sn', $this->order_sn);
        $this->db->update('order_info');			
		return $this->db->affected_rows();
    }
    
    /**
     * 修改运费
     * @return integer
     */
    public function change_shipping_fee(){
        $this->db->set('shipping_fee', $this->shipping_fee);
        $this->db->set('reason',  $this->reason);
        $this->db->set('operator',  $this->operator);
        $this->db->where('order_sn', $this->order_sn);
        $this->db->update('order_info');			
		return $this->db->affected_rows();
    }

    /**
     * 修改商品总价和运费
     * @return integer
     */
    public function change_total_price(){
        $this->db->set('goods_amount', $this->goods_amount);
        $this->db->set('shipping_fee', $this->shipping_fee);
        $this->db->set('reason',  $this->reason);
        $this->db->set('operator',  $this->operator);
        $this->db->where('order_sn', $this->order_sn);
        $this->db->update('order_info');			
		return $this->db->affected_rows();
    }

    /**
     * 获得购物车中商品的详细信息
     * @return array
     */
    public function get_carts($goods_sn, $act_sn){
        if(empty($goods_sn) && empty($act_sn)){
            return null; 
        }else{
            if(!empty($goods_sn) && empty($act_sn)){
                $strsql = " SELECT goods_sn sn,goods_name gname,price,stock,weight,thumb,is_sales,0 as goods_type,0 as rebate_rate,0 as rebate_cycle, 0 as  frequency,0 as group_number,0 as rebate_number FROM t_goods WHERE goods_sn IN (".$goods_sn.") ";
            }
            if(!empty($act_sn) && empty($goods_sn)){
                $strsql = " SELECT act_sn sn,act_name gname,act_price price,act_stock stock,act_weight weight,act_thumb thumb,IF(is_finished=1 or is_effective=0,0,1) is_sales,act_type as goods_type,rebate_rate,rebate_cycle,frequency,group_number,rebate_number FROM t_activity WHERE act_sn IN (".$act_sn.") ";
            }
            if(!empty($act_sn) && !empty($goods_sn)){
                $strsql = "SELECT goods_sn sn,goods_name gname,price,stock,weight,thumb,is_sales,0 as goods_type,0 as rebate_rate,0 as rebate_cycle, 0 as  frequency,0 as group_number,0 as rebate_number FROM t_goods WHERE goods_sn IN (".$goods_sn.") UNION SELECT act_sn sn,act_name gname,act_price price,act_stock stock,act_weight weight,act_thumb thumb,IF(is_finished=1 or is_effective=0,0,1) is_sales,act_type as goods_type,rebate_rate,rebate_cycle,frequency,group_number,rebate_number FROM t_activity WHERE act_sn IN (".$act_sn.") ";
            } 
            
            $query = $this->db->query($strsql);
            return $query->result_array();
        }
    }

    /**
    *获得购物车中商品的详细信息
    *@return array
    */
    public function get_orderdetailinfo($goods_sn, $attrid = 0){
        switch (substr($goods_sn,0,1)){
            case 'G':
                if($attrid == 0){
                    $strsql = " SELECT goods_sn sn,goods_name gname,price,stock,weight,thumb,is_sales,0 as goods_type,0 as rebate_rate,0 as rebate_cycle, 0 as  frequency,0 as group_number,0 as rebate_number FROM t_goods  WHERE goods_sn='".$goods_sn."'";    
                }else{
                    $strsql = " SELECT goods_sn sn,goods_name gname,attr_value,CASE attr_price WHEN 0 THEN price ELSE attr_price END price,stock,CASE attr_weight WHEN 0   THEN weight ELSE attr_weight END weight,thumb,is_sales,0 as goods_type,0 as rebate_rate,0 as rebate_cycle, 0 as  frequency,0 as group_number,0 as rebate_number FROM t_goods g INNER JOIN t_goods_attr a ON g.id = a.goods_id  WHERE goods_sn='".$goods_sn."' AND a.id=".$attrid;
                }
                break;
            case 'P':
                $strsql = " SELECT act_sn sn,act_name gname,act_price price,act_stock stock,act_weight weight,act_thumb thumb,IF(is_finished=1 or is_effective=0,0,1) is_sales,act_type as goods_type,rebate_rate,rebate_cycle,frequency,group_number,rebate_number FROM t_activity WHERE act_sn='".$goods_sn."'";
                break;
        }
        $query = $this->db->query($strsql);
        return $query->row_array(0);
    }

    /**
     * 获得数据库中购物车商品的详细信息
     * @return array
     */
    public function get_cartsfromdb($member_id){
        $strsql = "SELECT goods_sn sn,num,goods_name gname,price,stock,weight,thumb,is_sales,0 as goods_type,0 as rebate_rate,0 as rebate_cycle, 0 as  frequency,0 as group_number,0 as rebate_number,attr_id,attr_value,attr_price FROM t_goods g INNER JOIN t_car c ON g.goods_sn = c.product_sn LEFT JOIN t_goods_attr a ON c.attr_id = a.id AND g.id = a.goods_id WHERE custom_id=".$member_id." UNION SELECT act_sn sn,num,act_name gname,act_price price,act_stock stock,act_weight weight,act_thumb thumb,IF(is_finished=1 or is_effective=0,0,1) is_sales,act_type as goods_type,rebate_rate,rebate_cycle,frequency,group_number,rebate_number,null,null,null FROM t_activity,t_car WHERE act_sn=product_sn  AND custom_id =".$member_id;
        
        $query = $this->db->query($strsql);
        return $query->result_array();
    }

    /**
     * 获得订单列表
     * @param $order_status  订单状态 100=全部
     * @param $pay_status  支付状态 100=全部 
     * @param $order_sn  订单编号 
     * @param $telephone  买家电话  
     * @return array
     */ 
    public function get_orderlist($limit, $offset){
        if($this->order_status != 100){
            $this->db->where('order_status', $this->order_status);
        }
        if($this->pay_status != 100){
            $this->db->where('pay_status', $this->pay_status);
        }
        if(!empty($this->order_sn)){
            $this->db->where('order_sn', $this->order_sn);
        }
        if(!empty($this->telephone)){
            $this->db->where('telephone', $this->telephone);
        }
        if(!empty($this->member_id)){
            $this->db->where('member_id', $this->member_id);
		}
		$this->db->order_by('id', 'DESC');	  
		$this->db->limit($limit, $offset);
        $query = $this->db->get('order_info');
        return $query->result_array();
    }
    
    /**
	* 获取订单总数
	* @param $this->cat_id 商品类别 0全部
	* @return integer
	*/
	public function get_ordertotal(){
		if($this->order_status != 100){
			$this->db->where('order_status', $this->order_status);
		}
		if($this->pay_status != 100){
			$this->db->where('pay_status', $this->pay_status);
		}
        if(!empty($this->order_sn)){
			$this->db->where('order_sn', $this->order_sn);
		}
        if(!empty($this->telephone)){
			$this->db->where('telephone', $this->telephone);
		}
        if(!empty($this->member_id)){
			$this->db->where('member_id', $this->member_id);
		}
		$query = $this->db->get('order_info');
		return $query->num_rows();
	}

    /**
    * 根据订单编号获取商品信息
    * @return array
    */
    public function get_orderbysn(){			
		$this->db->where('order_sn', $this->order_sn);
        if(!empty($this->member_id)){
			$this->db->where('member_id', $this->member_id);
		}
		$query = $this->db->get('order_info');
		return $query->row_array(0);
    }

    /**
     * 生成支付单编号(两位随机 + 从2000-01-01 00:00:00 到现在的秒数+微秒+会员ID%1000)
     * 长度 =2位 + 10位 + 3位 + 3位  = 18位
     * 1000个会员同一微秒提订单，重复机率为1/100
     * @return string
     */
    private function make_sn($member_id){
        return mt_rand(10,99)
              . sprintf('%010d',time() - 946656000)
              . sprintf('%03d', (float) microtime() * 1000)
              . sprintf('%03d', (int) $member_id % 1000);
        //mt_srand((double) microtime() * 1000000);
        //return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    /*--------order_goods-----------------------------------------------------------------------------*/

    /**
	*获取订单商品列表
	*@param $order_sn订单集合
	*@return integer
	*/
	public function get_order_goodslist(){
        $this->db->where('order_sn', $this->order_sn);
		$query = $this->db->get('order_goods');
		return $query->result_array();
	}

    /**
	*获取订单商品列表
	*@param $order_sn订单集合
	*@return integer
	*/
	public function get_goods_list($order_sn){
        $strsql = "SELECT a.*,thumb FROM t_order_goods a inner JOIN t_goods b ON a.goods_sn = b.goods_sn WHERE order_sn IN (".$order_sn.") UNION
SELECT a.*,act_thumb thumb FROM t_order_goods a INNER JOIN t_activity b ON a.goods_sn = b.act_sn 
WHERE order_sn IN (".$order_sn.")";
		$query = $this->db->query($strsql);
		return $query->result_array();
	}

    /**
    *获取最新的团购商品信息
    *@return array
    */
    public function get_lastgroupingbysn($goods_sn){			
		$this->db->where('goods_sn', $goods_sn);
        $this->db->where('goods_status', 0);
        $this->db->or_where('goods_status', 1);
        $this->db->order_by('id','DESC');
		$query = $this->db->get('order_goods');
		return $query->row_array(0);
    }

    /**
    *修改订单商品状态
    *@return integer
    */
    public function set_goods_status($status){
        $this->db->set('goods_status',  $status);
        if(!empty($this->reason)){
            $this->db->set('refund_reason', $this->reason);
		}
        $this->db->where('order_sn', $this->order_sn);
        $this->db->update('order_goods');		
		return $this->db->affected_rows();
    }
    
    /**
    *修改单个订单商品状态
    *@return integer
    */
    public function set_goods_statusbyid(){
        $this->db->set('goods_status',  $this->order_status);
        if(!empty($this->reason)){
            $this->db->set('refund_reason', $this->reason);
		}
        if(!empty($this->solution)){
            $this->db->set('solution', $this->solution);
		}
        $this->db->set('refund_amount', $this->refund_amount);
        $this->db->where('id', $this->id);
        $this->db->update('order_goods');		
		return $this->db->affected_rows();
    }

    /**
    *根据id查询订单商品信息
    *@return array
    */
    public function get_order_goodsbyid(){
        $this->db->where('id', $this->id);
        $query = $this->db->get('order_goods');		
		return  $query->row_array(0);
    }

    /**
    *根据商品状态查询订单信息
    *@return array
    */
    public function get_goodslistbystatus($status){
        $this->db->select('a.*,member_id,order_status,pay_status,consignee,telephone,shipping_sn,delivery_time,finish_time');
        $this->db->from('order_goods a');
		$this->db->join('order_info b', 'a.order_sn = b.order_sn','inner');
        $this->db->where('a.goods_status', $status);
        $query = $this->db->get();
		return $query->result_array();
    }

    /**
	*获取订单商品列表
	*@param $order_sn订单集合
	*@return integer
	*/
	public function get_groupingbysn($id, $goods_sn){
        $strsql = "SELECT g.*,member_id FROM t_order_goods g,t_order_info i WHERE substring_index(grouping, '-', -1) = (SELECT substring_index(grouping, '-', -1) FROM t_order_goods WHERE id = ".$id.") AND goods_sn = '".$goods_sn."' AND (goods_status = 3 OR goods_status < 0) AND i.order_sn = g.order_sn;";
		$query = $this->db->query($strsql);
		return $query->result_array();
	}
}
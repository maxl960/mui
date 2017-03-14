<?php
class Comment_model extends CI_Model {
    public $id;
    public $goods_sn; //商品编码
    public $member_id; //会员id
    public $order_sn; //订单编号
    public $content; //评价内容
    public $rank; //评分
    public $add_time; //评价时间
    public $reply; //评价回复
    public $is_show; //是否显示

    /**
    *添加评论信息
    *@return integer
    */
    public function create(){
		$data = array(
	    	'goods_sn' => $this->goods_sn,
	        'member_id' => $this->member_id,
	        'order_sn' => $this->order_sn,
	        'content' => $this->content,
	        'rank' => $this->rank,
	        'is_show' => 1,
			'add_time' => time()
		);
		$this->db->insert('comment', $data);
        $rs = $this->db->insert_id();
        if($rs){
            $this->set_comment_status();
        }
        return $rs;
    }

    /**
    *根据id获取评价信息
    *@param order_sn
    *@param goods_sn  
    *@return array
    */
    public function get_commentbysn(){
        $this->db->where('order_sn',$this->order_sn);
        $this->db->where('goods_sn',$this->goods_sn);
        $query = $this->db->get('comment');
        return $query->row_array(0);
    }
    
    /**
    *设置状态
    *@param is_show
    *@param id
    *@return integer
    */
    public function set_status(){
        $this->db->set('is_show', $this->is_show);
        $this->db->where('id', $this->id);
        $this->db->update('comment');		
		return $this->db->affected_rows();
    }

    /**
    *评论回复
    *@param reply
    *@param id
    *@return integer
    */
    public function set_reply(){
        $this->db->set('reply', $this->reply);
        $this->db->where('id', $this->id);
        $this->db->update('comment');		
		return $this->db->affected_rows();
    }

    /**
    *获取评价列表
    *@param $goods_sn 商品编号
    *@param $limit  记录数 
	*@param $offset 起始位置
    *@return array
    */
    public function get_commentlist($limit, $offset){
        $gtype = substr($this->goods_sn,0,1);
        switch($gtype){
            case 'P':
                $this->db->select('c.*,act_name as goods_name');
                $this->db->from('comment c');
                $this->db->join('activity a', 'c.goods_sn = a.act_sn','inner');
                break;
            default:
                $this->db->select('c.*,goods_name');
                $this->db->from('comment c');
                $this->db->join('goods g', 'c.goods_sn = g.goods_sn','inner');
                break;
        }

        if($this->is_show == 1){
            $this->db->where('is_show', 1);
        }
        if(!empty($this->goods_sn)){
            $this->db->where('c.goods_sn', $this->goods_sn);
        }
        $this->db->order_by('c.id','DESC');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
	*获取商品评价总数
	*@param $goods_sn 商品编号
	*@return integer
	*/
	public function get_total(){
		if($this->is_show == 1){
            $this->db->where('is_show', 1);
        }
        if(!empty($this->goods_sn)){
            $this->db->where('goods_sn', $this->goods_sn);
        }
		$query = $this->db->get('comment');
		return $query->num_rows();
	}

    /**
    *修改评价状态
    *@param $order_sn
    *@param $goods_sn
    *@return integer
    */
    private function set_comment_status(){
        $this->db->set('assessed',  1);
        $this->db->where('order_sn', $this->order_sn);
        $this->db->where('goods_sn', $this->goods_sn);
        $this->db->update('order_goods');		
		return $this->db->affected_rows();
    }
}
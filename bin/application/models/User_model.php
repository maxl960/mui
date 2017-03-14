<?php
class User_model extends CI_Model {
	
    public $id;
    public $telephone;
    public $password;

    
    /**
     *添加用户
     *@return integer
     */
    public function create($data){
		if(!$this->telexist($data['telephone'])){
		$data['password']=md5($data['password']);	
		$data['reg_time'] = time();
        $this->db->insert('member', $data);
        return $this->db->affected_rows();
		}
		return false;
    }
	//检查用户手机号是否存在
	function telexist($tel) 
	{
		$wheredata = array('telephone'=> $tel);
		$this->db->where($wheredata);
		$this->db->select("telephone");
		$query = $this->db->get('t_member');
		if($query->num_rows() == 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	//登录
	//自动登录
	function autologin($data){
		$this->db->where('token',$data);
		$result=$this->db->get('t_member');
		if($result->num_rows() == 1){
		$query = $result->result_array();
		$udata['telephone'] =$query[0]['telephone'];
		$udata['usergroup'] =0;
		$udata['token']=$data;
		return $udata;
		}else{
		return false;
	}
	}
	//个人
	function login($userdata){
	$wheredata = array('telephone'=>$userdata['telephone'],'password'=>md5($userdata['password']));
	$this->db->where($wheredata);
	$this->db->select('id');
	$result=$this->db->get('t_member');
	if($result->num_rows() == 1){
	$query = $result->row_array();
	$this -> load -> library('common');
	$data['id'] = $query['id'];
	$data['user'] = $userdata['telephone'];
	$data['time'] = time();
	$str1 = $this ->common->encode($data);

	$this->db->where('id',$data['id']);
	@$data1 = array('token'=>$str1,'client_id'=>$userdata['cid'],'last_login'=>time());
	$this->db->update('t_member',$data1);
	$udata['telephone'] =$userdata['telephone'];
	$udata['token']=$str1;
	return $udata;
	}else{
		return false;
	}
	}
	//根据Id返回用户信息
	function get_userinfo($id){
		$this->db->where('id',$id);
		$result=$this->db->get('member');
		if($result->num_rows() == 1){
			$query = $result->row_array();
			return $query;
		}else{
			return false;
		}
		
	}
	//修改用户密码
	function editpass($data){
		$this->db->where('telephone',$data['telephone']);
		$data1=array('password'=>md5($data['password']));
		$this->db->update('t_member',$data1);
		$result = $this->db->affected_rows();
		if( $result == 1){
			return true;
		}else{
			return false;
		}
	}
	//检测用户地址条数
	function user_area($id){
		$this->db->where('member_id',$id);
		$result = $this->db->get('address');
		return $result->num_rows();
	}
	//列出用户收货地址
	function area_list($id){
		$this->db->where('member_id',$id);
		$result = $this->db->get('t_address');
		if($result->num_rows()>=1){
			return $result=$result->result_array();
		}else{
			return false;
		}
	}
	//添加地址
	function area_create($data){
		if($data['is_default']==1){
			$this->db->update('address',array('is_default'=>0),'member_id='.$data['member_id']);
		}
		$result = $this->db->insert('address',$data);
		return $this->db->insert_id();
		
	}
	//删除地址
	function delete_address($data){
		
		$this->db->where(array('id'=>$data['id'],'member_id'=>$data['uid']));
		$this->db->delete('address');
		return $this->db->affected_rows();
	}
	//修改默认地址	
	function change_default($data){
		$this->db->update('address',array('is_default'=>0),'member_id='.$data['uid']);
		$this->db->query('update t_address set is_default=1 where member_id='.$data['uid'].' and id!='.$data['id'].' limit 1');
		if($this->user_area($data['id'])>3){
			return false;
		}else{return true;}
	}
	//修改地址
	function edit_area($data){
		if($data['is_default']==1){
			$this->db->update('address',array('is_default'=>0),'member_id='.$data['member_id']);
		}
		$this->db->query('update t_address set is_default='.$data['is_default'].',consignee="'.$data['consignee'].'",telephone='.$data['telephone'].',province="'.$data['province'].'",city="'.$data['city'].'",district="'.$data['district'].'",address="'.$data['address'].'" where member_id='.$data['member_id'].' and id='.$data['id']);
		
		return $this->db->affected_rows();
	}
	//检测用户是否有默认地址
	function test_default($id){
		$this->db->where(array('member_id'=>$id,'is_default'=>1));
		$result = $this->db->get('address');
		if($result->num_rows()!=1){
			$data['uid']=$id;
			$data['id']=0;
			$this->change_default($data);
		}
		
	}
	//淘宝返利申请
	function ret_create($ret){
		$this->db->insert('rebate_application',$ret);
		return $this->db->affected_rows();
	}
	//个人返利表申请查询
	function ret_list($id){
		$result = $this->db->where('member_id',$id)->get('rebate_application');
		return $result->result_array();
	}
	//返利详情
	function ret_desc($order_sn){
		$result = $this->db->where('order_sn',$order_sn)->select('order_sn,amount,previous_rebate,last_rebate,rebate_cycle,frequency,returned,start_time,end_time')->get('rebate');
		return $result->row_array();
	}
	//检查单号是否有效
	function test_order($sn){
		$result = $this->db->where('order_sn',$sn['order_sn'])->get('rebate_application');
		if($result->num_rows()>0){
			return false;
		}else{
			return true;
		}
	}
	//用户账户增加+
	function  amount_plus($amounts){
		$this->db->where('id',$this->member_id)->set('amounts', 'amounts+'.$amounts, FALSE)->update('member');
		//var_dump($this->db->last_query());
		return $this->db->affected_rows();
	}
	//用户账户减少-
	function amount_minus($amounts){
		$this->db->where('id',$this->member_id)->set('amounts', 'amounts-'.$amounts, FALSE)->update('member');
		return $this->db->affected_rows();
	}
	//查询用户聊天记录
	function record_total($tel){
		$result = $this->db->where('from',$tel)->or_where('to',$tel)->get('customer_service_log');
		return $result->num_rows();
	}
	function record($tel,$limit,$offset){
		$result = $this->db->where('from',$tel)->or_where('to',$tel)->select('from,to,content,time')->order_by('time','DESC')->limit($limit, $offset)->get('customer_service_log');
		return $result->result_array();
	}
}
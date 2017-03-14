<?php
class Admin_model extends CI_Model {
	
    public $id;
    public $telephone;
    public $username;
    public $password;

	//管理员登录
	function adminlogin($data){
		$this->db->where($data);
		$result=$this->db->get('t_admin');
		if( $result->num_rows() ==1 ){
		$this -> load -> library('common');
		$id=$result->row_array();
		$dat['id'] = $id['id'];
		$dat['user'] = $data['name'];
		$dat['time'] = time();
		$str1 = $this ->common->encode($dat);
		$udata['name'] =$data['name'];
		$udata['token']=$str1;	
		return $udata;
		}else{
			return false;
		}
	}
	//根据Id返回用户信息
	function get_userinfo($id){
		$this->db->where('id',$id);
		$result=$this->db->get('t_admin');
		if($result->num_rows() == 1){
			$query = $result->row_array();
			return $query;
		}else{
			return false;
		}
		
	}
	//显示用户列表
	function userpre($limit,$offset){
		$result = $this->db->limit($limit,$offset)->get('member');
		$query1 = $result->result_array();
		return $query1;	
	}
	//用户总数
	function getuser_total(){
		$num = $this->db->get('member');
		return $num->num_rows();
	}
	//广告总数
	function getad_total(){
		$num = $this->db->get('advertisement');
		return $num->num_rows();
	}
	//广告列表
	function ad_list($limit,$offset){
		$result = $this->db->limit($limit,$offset)->get('advertisement');
		return $result->result_array();
	}
	//广告添加
	function ad_create($data){
		$result = $this->db->insert('advertisement',$data);
		return $this->db->affected_rows();
	}
	//广告有效条数
	function ad_active(){
		$result = $this->db->where('enabled',1)->get('advertisement');
		return $result->num_rows();
	}
	//广告修改状态
	function status_change(){
		$this->db->where('id',$this->ad_id)->update('advertisement',array('enabled'=>$this->ad_status));
	}
	//广告删除
	function ad_delete(){
		$result = $this->db->where('id',$this->ad_id)->select('picurl')->get('advertisement');
		if($result->row_array()){
			$pic=$result->row_array();
			if(file_exists($pic['picurl'])){

			unlink($pic['picurl']);
		} 
		}
		$this->db->where('id',$this->ad_id)->delete('advertisement');
	}
	//广告显示
	function show_ad($id){
		$result = $this->db->where('id',$id)->get('advertisement');
		return $result->row_array();
		
	}
	//编辑广告
	function ad_edit($data){
		if(isset($data['picurl'])){
			$result = $this->db->where('id',$data['id'])->select('picurl')->get('advertisement');
			$pic=$result->row_array();
			unlink($pic['picurl']);
		}
		$this->db->where('id',$data['id'])->update('advertisement',$data);
		return $this->db->affected_rows();
	}
	//返利申请列表
	function rebate_total(){
		$num= $this->db->get('rebate_application');
		return $num->num_rows();
	}
	function rebate_list($limit,$offset){
		$result = $this->db->limit($limit,$offset)->get('rebate_application');
		return $result->result_array();
	}
	//获取返利数据
	function rebate_data($order_sn){
		$result = $this->db->where('order_sn',$order_sn)->get('rebate_application');
		return $result->row_array();
	}
	//拒绝返利
	function rebate_refuse($data){
		$dat=$data;
		unset($dat['order_sn']);
		$this->db->where('order_sn',$data['order_sn'])->update('rebate_application',$dat);
	}
	//添加返利规则
	function rebate_create($data){
		$this->db->insert('rebate',$data);
		$query=$this->db->insert_id();
		if($query>0){
			$this->db->update('rebate_application',array('status'=>1),array('order_sn'=>$data['order_sn']));
		}
	}
	//添加客服
	function create_service($data){
		$this->db->insert('customer_service_executive',$data);
		return $this->db->insert_id();
		
	}
	//客服列表
	function service_list(){
		$result = $this->db->select('id,name,telephone,status')->get('customer_service_executive');
		return $result->result_array();
	}
	function service_desc($id){
		$result = $this->db->where('id',$id)->get('customer_service_executive');
		return $result->row_array();
	}
	//客服修改
	function service_update($data){
		$this->db->update('customer_service_executive',array('name'=>$data['name'],'password'=>$data['password'],'status'=>$data['status']),array('id'=>$data['id']));
		return $this->db->affected_rows();
	}
	//获取用户
	function find_cus($tel){
		$result = $this->db->where('from',$tel)->or_where('to',$tel)->select('to,from')->get('customer_service_log');
		return $result->result_array();
	}
	//获取客服名字
	function server_name($tel){
		$result = $this->db->where('telephone',$tel)->select('name')->get('customer_service_executive');
		$result = $result->row_array();
		return $result['name'];
	}
	//获取信息
	function message_list($data){
		 $where = 'from='.$data['server'].' AND to='.$data['custom'].' OR from='.$data['custom'].' AND to='.$data['server'];
		$result = $this->db->where($where)->order_by('time','ASC')->get('customer_service_log');
		return $result->result_array();
	}
	//客服登录
	function slogin($data){
		$result = $this->db->where('telephone',$data['telephone'])->select('password,id,name')->get('customer_service_executive');
		$res = $result->row_array();
		if($result->num_rows()==1 && md5($data['password'])==$res['password']){
			
			$d['id'] = $res['id'];
			$d['name'] = $res['name'];
			$dat['name']=$res['name'];
			$dat['time']=time();
			$d['token']=$this->common->encode($dat);
			$this->db->update('customer_service_executive',array('token'=>$d['token']),array('telephone'=>$data['telephone']));
			
			return $d;
		}else{
			return false;
		}
	}
}
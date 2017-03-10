<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Category extends CI_Controller{
	public function __construct() {
        header("Access-Control-Allow-Origin:*");
        parent::__construct();
        $this->load->database();
    }
	public function show(){
		//$this->db->where('is_show', 1);
		$query = $this->db->get('category');
        $list=$query->result_array();
        $data=array();
        foreach ($list as $key => $value) {
        	$id=$value['id'];
        	if($value['parentid']==0){
        		$data[$id]=$value;
        		$data[$id]['son']=array();
        	}else{
        		$pid=$value['parentid'];
        		$data[$pid]['son'][$id]=$value;
        		$this->db->where('cat_id',$pid);
        		$goods=$this->db->get('goods')->result_array();
        		if(count($goods)==1){
        			//$data[$pid]['goods']
        			//print_r($goods['id']);
        			$gl=array();
        			foreach ($goods as $k => $v) {
        				//echo json_encode($v);
        				$gl[$v['id']]=$v;
        			}
        			$data[$pid]['goods']=$gl;
        		}
        		//$data[$pid]['goods'][$goods]=$value;
        	}
        }
		echo json_encode($data);
	}
}
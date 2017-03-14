<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('goods_model');
        $this->load->model('activity_model');
		$this->load->model('ad_model');
    }
    
    /**
	 * 获取首页商品信息
     * @param goods_num 返回商品信息数量
	 * @param rebate_num 返回返利信息数量
	 * @param group_num 返回团购信息数量
	 * @return json
	 */
	public function get_infolist(){
        $goods_num = empty($_POST['goods_num']) ? 4 : $_POST['goods_num'];
		$rebate_num = empty($_POST['rebate_num']) ? 4 : $_POST['rebate_num'];
	    $group_num = empty($_POST['group_num']) ? 4 : $_POST['group_num'];
    	$this->goods_model->is_sales = 1;
    	//print_r(expression)
		$goods_list = $this->goods_model->get_goods($goods_num, 0, 'sort_order', 'DESC');
		$goods_arr = array();
		foreach ($goods_list as $key => $val) {
			$goods_arr[$key]['id'] = $val['id'];
			$goods_arr[$key]['goods_name'] = $val['goods_name'];	
			$goods_arr[$key]['price'] = $val['price'];
			$goods_arr[$key]['thumb'] = $val['thumb'];
			$goods_arr[$key]['weight'] = $val['weight'];			
		}
        
        $this->activity_model->act_type = 1;
        $this->activity_model->is_effective = 1;
		$rebate_list = $this->activity_model->get_activitylist($rebate_num, 0);
		$rebate_arr = array();
		foreach ($rebate_list as $key => $val) {
			$rebate_arr[$key]['id'] = $val['id'];
			$rebate_arr[$key]['act_name'] = $val['act_name'];	
			$rebate_arr[$key]['start_time'] = $val['start_time'];
            $rebate_arr[$key]['end_time'] = $val['end_time'];	
			$rebate_arr[$key]['act_price'] = $val['act_price'];	
			$rebate_arr[$key]['act_thumb'] = $val['act_thumb'];
			$goods_arr[$key]['act_weight'] = $val['act_weight'];	
			$rebate_arr[$key]['is_finished'] = $val['is_finished'];			
		}

        $this->activity_model->act_type = 2;
        $this->activity_model->is_effective = 1;
		$group_list = $this->activity_model->get_activitylist($group_num, 0);
		$group_arr = array();
		foreach ($group_list as $key => $val) {
			$group_arr[$key]['id'] = $val['id'];
			$group_arr[$key]['act_name'] = $val['act_name'];	
			$group_arr[$key]['start_time'] = $val['start_time'];
            $group_arr[$key]['end_time'] = $val['end_time'];	
			$group_arr[$key]['act_price'] = $val['act_price'];	
			$group_arr[$key]['act_thumb'] = $val['act_thumb'];
			$goods_arr[$key]['act_weight'] = $val['act_weight'];	
			$group_arr[$key]['is_finished'] = $val['is_finished'];			
		}
		$ad_list = $this->ad_model->get_ads();
		$ad_arr = array();
		foreach ($ad_list as $key => $val) {
			$ad_arr[$key]['ad_name'] = $val['ad_name'];
			$ad_arr[$key]['ad_link'] = $val['ad_link'];
			$ad_arr[$key]['picurl'] = $val['picurl'];			
		}
    	$data['code'] = 200;
		$data['datas'] = array('goodslist' => $goods_arr,'rebatelist' => $rebate_arr,'grouplist' => $group_arr,'adlist' => $ad_arr);	
		echo json_encode($data);
	}		
}
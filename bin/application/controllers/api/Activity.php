<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity extends CI_Controller {
	public function __construct(){
        parent::__construct();
        $this->load->model('activity_model');
    }
    
	/**
	 * 根据id获取活动的详细信息
     * @param actid 活动id
	 * @return json
	 */
	public function get_activitybyid(){
		$actid = $this->input->post('actid');
		if(empty($actid)){
			$msg = array('code' => 400, 'datas'=> array('error' => '没有数据'));	
			echo json_encode($msg);
		}else{
			$this->activity_model->id = $actid;
			$activity = $this->activity_model->get_activityinfo();
			$this->load->model('pictures_model');
			$this->pictures_model->goods_id = $activity['goods_id'];
			$pictures = $this->pictures_model->get_picturesbygid();
			$data['code'] = 200;
			$data['datas'] = array('activity' => $activity, 'pictures' => $pictures,'now' => time());
			echo json_encode($data);
		}
	}
	
	/**
	 * 根据分类获取活动列表
	 * @param acttype 活动类别 1=返利,2=团购
	 * @param page 当前页
	 * @param $sort 排序方式 id,start_time,end_time
	 * @param $order ASC=升序 DESC = 降序
	 * @return json
	 */
	public function get_activitylist(){
		$acttype = empty($_POST['acttype']) ? 0 : $_POST['acttype'];
		$page = empty($_POST['page']) ? 1 : $_POST['page'];
		$sort = empty($_POST['sort']) ? 'id' : $_POST['sort'];
	    $order = empty($_POST['order']) ? 'DESC' : $_POST['order'];

        $limit = 15;
        $offset = ($page-1)*$limit;
		$this->activity_model->act_type = $acttype;
        $this->activity_model->is_effective = 1;
		$rows = intval($this->activity_model->get_total());
		$totalpage = ceil($rows/$limit);
		$list = $this->activity_model->get_activitylist($limit, $offset, $sort, $order);
		
		$arr = array();
		foreach ($list as $key => $val) {
			$arr[$key]['id'] = $val['id'];
			$arr[$key]['act_sn'] = $val['act_sn'];
			$arr[$key]['act_name'] = $val['act_name'];	
			$arr[$key]['start_time'] = $val['start_time'];
            $arr[$key]['end_time'] = $val['end_time'];	
			$arr[$key]['act_price'] = $val['act_price'];	
			$arr[$key]['act_stock'] = $val['act_stock'];
			$arr[$key]['act_thumb'] = $val['act_thumb'];
			$arr[$key]['act_weight'] = $val['act_weight'];
			$arr[$key]['is_sales'] = $val['is_sales'];			
		}
    	$data['code'] = 200;
		$data['pages'] = $totalpage;
		$data['datas'] = array('actlist' => $arr,'now' => time());	
		echo json_encode($data);
	}	
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Record extends Member_Controller {
    public function __construct() {
        parent::__construct();
		$this->load->model('user_model');
     
    }
	public function list_record(){
		$page = empty($_POST['page']) ? 1 : $_POST['page'];
		$tel = $this->input->post('telephone');
		$limit = 10;
        $offset = ($page-1)*$limit;
		$rows = intval($this->user_model->record_total($tel));
		$data['totalpage'] = ceil($rows/$limit);
		$data['list'] = $this->user_model->record($tel,$limit,$offset);
		if(count($data['list'])>0){
			for($i=0;$i<count($data['list']);$i++){
				if($data['list'][$i]['from'] == $tel){
					$data['list'][$i]['to'] = '客服';
						
				}else{
					$data['list'][$i]['from'] = '客服';
				}
			}
			
		}
		$data['code'] = 200;
		echo json_encode($data);
		
	}
}
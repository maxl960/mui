<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat extends CI_Controller {
    public function __construct() {
        parent::__construct();
		$this->load->library('session');
    }

    public function home(){
        $token = $this->session->token;
        if(empty($token)){
            redirect(base_url('admini/admin/serlogin'));
        }else{
            $this->db->where('token', $token);
		    $query = $this->db->get('customer_service_executive');
		    $row = $query->row_array(0);
            if(empty($row)){
                redirect(base_url('admini/admin/serlogin'));
            }else{
                $data['telephone'] = $this->session->telephone;
                $data['token'] = $token;
                $this->load->view('chat/home',$data);
            } 
        }
    }
}
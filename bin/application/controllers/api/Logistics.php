<?php
defined('BASEPATH') OR exit('No direct script access allowed');

defined('EBusinessID') OR define('EBusinessID', 1272699);
defined('AppKey') OR define('AppKey', 'd6aa29d9-c28c-4558-940a-d6f066c79c74');
defined('ReqURL') OR define('ReqURL', 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx');
    
class Logistics extends CI_Controller {

    public function __construct(){
        parent::__construct();
    }
    
    /**
    *Json方式 查询订单物流轨迹
    */
    public function get_ordertraces(){
        $shipper_code = $this->input->post('shipper_code');
        $logistic_code = $this->input->post('logistic_code');
        
        if(empty($shipper_code) || empty($logistic_code)){
			$msg = array('code' => 400, 'datas'=> array('error' => '查询错误'));	
			echo json_encode($msg);
		}else{
            $arr = array('OrderCode' => '', 'ShipperCode'=>$shipper_code, 'LogisticCode'=>$logistic_code);
            $requestData = json_encode($arr);  
            $datas = array(
                'EBusinessID' => EBusinessID,
                'RequestType' => '1002',
                'RequestData' => urlencode($requestData) ,
                'DataType' => '2',
            );
            $datas['DataSign'] = $this->encrypt($requestData, AppKey);
            $result = $this->send_post(ReqURL, $datas);	
            $data['code'] = 200;
            $data['datas'] = json_decode($result);
            echo json_encode($data);
        }
    }
    
    /**
    *  post提交数据 
    * @param  string $url 请求Url
    * @param  array $datas 提交的数据 
    * @return url响应返回的html
    */
    private function send_post($url, $datas) {
        $temps = array();	
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);		
        }	
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if(!isset($url_info['port'])){
            $url_info['port']=80;	
        }
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd);  
        
        return $gets;
    }

    /**
    *电商Sign签名生成
    *@param data 内容   
    *@param appkey Appkey
    *@return DataSign签名
    */
    private function encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data.$appkey)));
    }
}
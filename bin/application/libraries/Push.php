<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Push{
	protected $CI;
    
    private $appid = 'Z4ESqe0Mgw58d9c0vvRwm8';
	private $appkey = 'zDTqE5E4oU94FKe574xZ05';
	private $master_secret = 'iB6iW5Wkrb5KixVmAFRGa1';
	    
    public function __construct(){
        $this->CI =& get_instance();
    }
    
	/**
	* 推送个人消息
	* @param cid 设备ClientID
	* @param array msg 消息内容 
	*/
	public function pushMessageToSingle($cid, $msg){
	    $params = array('domainUrl' => NULL, 'appkey' => $this->appkey, 'masterSecret' => $this->master_secret, 'ssl' => false);
		$this->CI->load->library('push/IGeTui',$params);
	    //消息模版：
	    $template = $this->IGtTransmissionTemplate($msg);
	    //个推信息体
	    $message = new IGtSingleMessage();
	    $message->set_isOffline(true);//是否离线
	    $message->set_offlineExpireTime(3600*12*1000);//离线时间
	    $message->set_data($template);//设置推送消息类型
	    
	    //接收方
	    $target = new IGtTarget();
	    $target->set_appId($this->appid);
	    $target->set_clientId($cid);
	    
	    try{
	        $rep = $this->CI->igetui->pushMessageToSingle($message, $target);
	        var_dump($rep);
	    }catch(RequestException $e){
	        $requstId = e.getRequestId();
	        $rep = $this->CI->igetui->pushMessageToSingle($message, $target,$requstId);
	        var_dump($rep);
	    }
	}
    
    /**
	* 群推消息
	* @param taskname 任务组名
	* @param array msg 消息内容 
	*/
    public function pushMessageToApp($taskname,$msg){
	    $params = array('domainUrl' => NULL, 'appkey' => $this->appkey, 'masterSecret' => $this->master_secret, 'ssl' => false);
		$this->CI->load->library('push/IGeTui',$params);
	    //消息模版：
	    $template = $this->IGtTransmissionTemplate($msg);
	    //个推信息体
	    //基于应用消息体
	    $message = new IGtAppMessage();
	    $message->set_isOffline(true);
	    $message->set_offlineExpireTime(10 * 60 * 1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
	    $message->set_data($template);

	    $appIdList=array($this->appid);
	    $message->set_appIdList($appIdList);
	    $rep = $this->CI->igetui->pushMessageToApp($message, $taskname);
	    var_dump($rep);
	}
	
	/**
	* 消息推送模板
	* @param array msg 消息内容 
	*/
	private function IGtTransmissionTemplate($msg){
	    $template =  new IGtTransmissionTemplate();
	    $template->set_appId($this->appid);//应用appid
	    $template->set_appkey($this->appkey);//应用appkey
	    $template->set_transmissionType(2);//透传消息类型
	    $template->set_transmissionContent(json_encode($msg));//透传内容
	    //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息

	    //APN高级推送
	    $apn = new IGtAPNPayload();
	    $alertmsg = new DictionaryAlertMsg();
	    $alertmsg->body = $msg['content'];
	    $alertmsg->actionLocKey = "ActionLockey";
	    $alertmsg->locKey = "LocKey";
	    $alertmsg->locArgs = array("locargs");
	    $alertmsg->launchImage = "";
		//IOS8.2 支持
	    $alertmsg->title = $msg['title'];
	    $alertmsg->titleLocKey = "TitleLocKey";
	    $alertmsg->titleLocArgs = array("TitleLocArg");

	    $apn->alertMsg = $alertmsg;
	    $apn->badge = 7;
	    $apn->sound = "";
	    $apn->add_customMsg("payload", $msg['payload']);
	    $apn->contentAvailable = 1;
	    $apn->category = "ACTIONABLE";
	    $template->set_apnInfo($apn);
	    return $template;
	}  		 	  		 	
}
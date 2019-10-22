<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cguestmsg{	
	
	function  __construct() {		
		$this->CI =& get_instance();        
        $this->CI->load->model('mguestmsg');		
	}	
	
	function save_guest_msg($data){		
		$data['cid'] = 0;		
		$data['cserver'] = $_SERVER['SERVER_NAME'];
		if(isset($data['name'])){
			$data['cname'] = urlencode($data['name']);
		}
		if(isset($data['email'])){
			$data['cemail'] = urlencode($data['email']);
		}
		if(isset($data['msg'])){
			$data['ctext'] = urlencode($data['msg']);
		}
		
		$this->CI->load->library('clottery_sms');
		$date = date('Y-m-d H:i:s');
		$text = 'message:

';
		$text .= substr($data['msg'], 0, 50);
		$this->CI->clottery_sms->send_sms('0','0129373281',$text);
		
		return $this->CI->mguestmsg->save_guest_msg($data);
	}
	
}

?>
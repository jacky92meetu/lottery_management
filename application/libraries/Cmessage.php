<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cmessage{
    
    function  __construct() {
		$this->CI =& get_instance();		
	}
	
	function set_message_url($message='',$type='notice',$url=''){
		$this->set_response_message($message, $type);
		if(strlen($url)>0){
			$this->CI->load->helper('url');			
			redirect($url);
		}
	}
	
	function set_response_message($message='',$type='notice',$url=''){
		if(empty($message) || strlen($message)===FALSE){
			return false;
		}		
		$data = $this->get_response_message();
		if(!$data){
			$data = array();
		}
		$data[] = array("message"=>$message,"type"=>$type);
		
		$data = serialize($data);
		$_SESSION['response_message'] = $data;
		if(strlen($url)>0){
			$this->CI->load->helper('url');			
			redirect($url);
		}
		return true;
	}

	function get_response_message(){
		if(ISSET($_SESSION['response_message']) && !EMPTY($_SESSION['response_message'])){			
			return unserialize($_SESSION['response_message']);
		}		
		return false;
	}
	
	function del_response_message(){
		if(ISSET($_SESSION['response_message'])){			
			$_SESSION['response_message'] = null;
			return true;
		}		
		return false;
	}
}

?>

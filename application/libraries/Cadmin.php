<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CAdmin{

	function  __construct() {
		$this->CI =& get_instance();
		$this->CI->load->library('cuser');
		$this->CI->load->model('madmin');
	}

	function check_page_access(){
		if($this->is_login()){
			return true;
		}
		$this->CI->cuser->logout();
		$this->CI->load->helper('url');
		redirect(base_url().'admin/login?return='.urlencode($_SERVER['REQUEST_URI']));
		return false;
	}
	
	function check_allow_dns(){
		/*
		$this->CI->load->library('ccfg');		
		$result = $this->CI->ccfg->get('allow_dns');
		if(strlen($result)>0){
			$result = explode("\n",$result);
			foreach($result as $value){
                                $value = trim($value);
				if(preg_match('#[a-z]+#Ui', $value)){
					$ip = gethostbyname($value);
				}else{
					$ip = trim($value);
				}				
				$cip = $_SERVER['REMOTE_ADDR'];				
				if($ip==$cip){
					return true;
				}
			}
		}
		return false;
		 * 
		 */
		return true;
	}
	
	function is_login(){
		return $this->CI->cuser->is_login();
	}
	
	function& getUser($id = null){
		return $this->CI->cuser->getUser($id);
	}
	
	function& getLoginUser(){
		return $this->CI->cuser->getLoginUser();
	}
	
	function login($data = array()){
		$data = array_merge(array('username'=>'','password'=>'','type'=>'2'),$data);		
		return $this->CI->cuser->login($data);
	}
	
	function logout(){					
		$this->CI->cuser->logout();
		$this->CI->load->helper('url');
		redirect(base_url().'admin/login');
	}   
    
    function func_call($classname = "", $method = "index", $params = array()){		
		static $instances = array();		
		if(empty($classname) || strlen($classname)==0){
			return false;
		}
		if(!isset($instances[$classname])){
			$cn = "AdminController".$classname;				
			$file = APPPATH."admin/controllers/".$classname.EXT;
			if(!is_file($file)){
				return false;
			}
			require_once($file);
			if(!class_exists($cn)){
				return false;
			}					
			$instances[$classname] = new $cn;
		}
		if(method_exists($instances[$classname], $method)){
			$this->func_class = $classname;
			$this->func_method = $method;
			call_user_func_array(array(&$instances[$classname], $method), $params);
			return true;
		}
		return false;
	}
}

?>
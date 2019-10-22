<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ccfg{
	
	var $cfg = array();		
    
    function  __construct() {
		$this->CI =& get_instance();		
		$this->CI->load->model('mcfg');
		$this->cfg = $this->_get_cfg();
	}
	
	function get($var = '', $default = ''){
		if(isset($this->cfg[$var])){
			return $this->cfg[$var];
		}		
		return $default;
	}
	
	function sys_get($var = '', $default = ''){		
		$result = $this->CI->mcfg->get_sys_cfg($var);
		if($result){
			return $result[0]['cvalue'];
		}
		return $default;
	}
	
	function sys_flush($var = '', $default = ''){				
		$result = $this->CI->mcfg->get_sys_cfg($var);
		if($result){
			$this->sys_set($var,'');
			return $result[0]['cvalue'];
		}
		return $default;
	}
	
	function set($var = '', $val = ''){
		if(strlen($var)>0){
			return $this->_update_config_db($var, $val);
		}
		return false;
	}
	
	function sys_set($var,$value){				
		$result = $this->CI->mcfg->sys_cfg_update($var,$value);
		if($result){
			return true;
		}
		return false;
	}
	
	function sys_del($var){				
		$result = $this->CI->mcfg->sys_cfg_delete($var);
		if($result){
			return true;
		}
		return false;
	}
	
	function _get_cfg(){						
		$result = $this->CI->mcfg->config_list_all();
		if($result){
			foreach($result as $value){
				$this->cfg[$value['cname']] = $value['cvalue'];
			}
			return $this->cfg;
		}
		return false;
	}
	
	function _update_config_db($var = '', $val = ''){				
		if(strlen($var)<=0){
			return false;
		}		
		$data = array("var"=>$var,"val"=>$val);
		return $this->CI->mcfg->config_update($data);
	}
	
	function sys_is_timeout($var){
		$result = $this->CI->mcfg->get_sys_cfg($var);
		if($result){
			$cdate = date("Y-m-d H:i:s");
			$date = $result[0]['ctdate'];
			if(strtotime($cdate)<=strtotime($date)){
				return false;
			}			
		}
		return true;
	}
	
	function sys_set_timeout($var, $timeout = 5){				
		$date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s"))+$timeout);
		return $this->CI->mcfg->sys_cfg_timeout($var,$date);
	}
	
	function sys_get_status($var){				
		$result = $this->CI->mcfg->get_sys_cfg($var);
		if($result){
			return $result[0]['cstatus'];
		}
		return false;
	}
	
	function sys_set_status($var, $value = 0){		
		return $this->CI->mcfg->sys_cfg_status_update($var,$value);
	}
	
	function sys_check_cronjob($var){		
		if($this->sys_get_status($var)=="0"){
			return $this->sys_set_status($var, 1);			
		}		
		return false;
	}
}

?>
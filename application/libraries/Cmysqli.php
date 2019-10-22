<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMysqli {
	var $db = null;	
	var $sp_db = null;
	var $group_name = "default";
	var $query_list;	
	
	function __construct($config = array())
	{	
		if (count($config) > 0){
			$this->initialize($config);
		}
		$this->query_list = array();
		$this->CI =& get_instance();
		$this->db = $this->CI->load->database($this->group_name,TRUE);
	}

	function  __destruct() {
        
	}
	
	function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			$this->$key = $val;
		}
	}
	
	function add_query_list($query,$timer){
		$this->query_list[] = array("query"=>$query,"timer"=>$timer);
	}
	
	function get_query_list(){
		return $this->query_list;
	}
	
	function create_sp_db(){
		if($this->sp_db){
			$this->sp_db->close();
		}
		$file_path = APPPATH.'config/'.ENVIRONMENT.'/database'.EXT;		
		if ( ! file_exists($file_path))
		{			
			$file_path = APPPATH.'config/database'.EXT;			
		}		
		include($file_path);		
		$database = $db[$this->group_name];
		$database['dbdriver'] = 'mysqli';
		$this->sp_db = $this->CI->load->database($database,TRUE);
		$this->sp_db->query('SET NAMES utf8');
	}

	private function _exec($proc = null, $params_list = null, $return = FALSE){
		$result = false;		

		if(strlen($proc)>0){
			$params = array();
			for($i=0; $i<sizeof($params_list); $i++){				
				if(is_string($params_list[$i])){
					$params[]="'".$params_list[$i]."'";
				}else{
					$params[]= $params_list[$i];
				}
			}
			$params = implode(",",$params);

			$prepare_stmt = "CALL ".$proc." (".$params.")";			
			try{				
				$time1 = microtime(true);
				$this->create_sp_db();
				$result = $this->sp_db->query($prepare_stmt,FALSE,$return);
				$this->sp_db->close();
				$time1 = (microtime(true) - $time1);
				$this->add_query_list($prepare_stmt, round($time1,3));
				if(!$result){					
					//show_error($this->sp_db->_error_message());
				}				
			}catch(Exception $e){				
				//show_error($e->getMessage());
			}
		}

		return $result;
	}

	public function call($proc = null){
		$params = func_get_args();
		array_shift($params);
		$result = $this->_exec($proc,$params,TRUE);
		if($result){						
			$temp = array();
			try{
				if(method_exists($result, "num_rows") && $result->num_rows()){
					$temp = $result->result_array();
					$result->free_result();
					if(sizeof($temp)>0){
						return $temp;
					}					
				}
			}catch(Exception $e){
				return false;
			}
		}		
		return false;
	}
	
	function run($query=''){
		if(strlen($query)==0){
			return false;
		}
		try{
			$time1 = microtime(true);			
			$result = $this->db->query($query);
			$time1 = (microtime(true) - $time1);
			$this->add_query_list($query, round($time1,3));
			if($result){				
				return true;
			}else{
				//show_error($this->db->_error_message());
			}						
		}catch(Exception $e){
			//show_error($e->getMessage());
		}				
		return false;
	}
	
	function result($query=''){
		if(strlen($query)==0){
			return false;
		}
		try{
			$time1 = microtime(true);			
			$result = $this->db->query($query);
			$time1 = (microtime(true) - $time1);
			$this->add_query_list($query, round($time1,3));
			if($result){
				if(method_exists($result, "num_rows") && $result->num_rows()){
					$temp = $result->result_array();
					$result->free_result();
					if(sizeof($temp)>0){
						return $temp;
					}					
				}
			}else{
				//show_error($this->db->_error_message());
			}						
		}catch(Exception $e){
			//show_error($e->getMessage());
		}				
		return false;
	}
}
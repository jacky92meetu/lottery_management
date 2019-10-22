<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mcfg extends CI_Model {

    function __construct()
    {
        parent::__construct();		
    }
		    
    function config_update($data = array()){
		$config = $this->config_list_all($data['var']);
		if($config){
			$query = "UPDATE config SET cvalue='".$data['val']."' WHERE cname='".$data['var']."' LIMIT 1";
			return $this->cmysqli->run($query);
		}else{
			$query = "INSERT INTO config(cname,cvalue) VALUES('".$data['var']."','".$data['val']."')";
			return $this->cmysqli->run($query);
		}
		return false;
	}	
	
	function get_config_by_id($id){
		$query = 'SELECT a.id,a.cname,a.cvalue FROM config a WHERE a.id="'.$id.'" order by id';		
		return $this->cmysqli->result($query);
	}
	
	function config_list_all($var = null){
		$query = 'SELECT a.id,a.cname,a.cvalue FROM config a';
		if(!is_null($var)){
			$query .= ' WHERE a.cname="'.$var.'" ';
		}
		$query .= ' order by id';
		return $this->cmysqli->result($query);
	}
	
	function config_list($var = null){
		$query = 'SELECT a.id,a.cname,a.cvalue FROM config a';
		if(!is_null($var)){
			$query .= ' WHERE a.cname="'.$var.'" ';
		}		
		
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	}
	
	function get_sys_cfg($var){
		$query = 'select a.* from system_status a where a.cname="'.$var.'" limit 1';
		return $this->cmysqli->result($query);
	}
	
	function sys_cfg_update($var,$value){
		if($this->get_sys_cfg($var)){
			$query = 'update system_status set cvalue="'.$value.'" where cname="'.$var.'" limit 1';
			return $this->cmysqli->run($query);			
		}else{
			return $this->sys_cfg_add($var, $value);
		}		
	}
	
	function sys_cfg_status_update($var,$value = 0){
		$query = 'update system_status set cstatus="'.$value.'" where cname="'.$var.'" limit 1';
		return $this->cmysqli->run($query);
	}
	
	function sys_cfg_add($var,$value){
		$query = 'INSERT INTO system_status(`cname`,`cvalue`) VALUES("'.$var.'","'.$value.'")';
		return $this->cmysqli->run($query);
	}
	
	function sys_cfg_delete($var){
		$query = 'DELETE FROM system_status WHERE cname="'.$var.'" LIMIT 1';
		return $this->cmysqli->run($query);
	}
	
	function sys_cfg_timeout($var, $tdate){		
		if($this->get_sys_cfg($var)){			
		}else{
			$this->sys_cfg_add($var, '');
		}	
		$query = 'update system_status set ctdate="'.$tdate.'" where cname="'.$var.'" limit 1';
		return $this->cmysqli->run($query);		
	}
}

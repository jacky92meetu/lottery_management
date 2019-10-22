<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mlottery_waiting_list extends CI_Model {
	
	var $db = null;

    function __construct()
    {
        parent::__construct();
		//$this->load->library('cmysqli',array('group_name'=>'flexbile'),'flexbile_mysqli');
    }
	
	function web_get_waiting_list(){
		$query = 'SELECT * FROM (SELECT a.* FROM lottery_waiting_list a order by ccreated_date desc) a';
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	}
	
	function get_waiting_list_mnc_check_list(){		
		$query = "SELECT a.* FROM lottery_waiting_list a WHERE a.cstatus=0 ";
		$query.=' LIMIT 10';
		return $this->cmysqli->result($query);
	}
	
	function get_waiting_list_processing_list(){		
		$query = "SELECT a.* FROM lottery_waiting_list a WHERE a.cstatus=5 ";
		$query.=' LIMIT 10';
		return $this->cmysqli->result($query);
	}
	
	function get_waiting_list_by_id($id){
		$query = 'SELECT a.* FROM lottery_waiting_list a WHERE a.id="'.$id.'"';
        return $this->cmysqli->result($query);
	} 
	
	function get_waiting_list_by_pts($phone=null,$type=null,$status=null,$date=null,$remarks=null){
		$query = 'SELECT a.* FROM lottery_waiting_list a WHERE 1=1 ';
		if(strlen($phone)>0){
			$query .= ' and a.cphone_no="'.$phone.'" ';
		}
		if(strlen($type)>0){
			$query .= ' and a.ctype="'.$type.'" ';
		}
		if(strlen($status)>0){
			$query .= ' and a.cstatus="'.$status.'" ';
		}
		if(strlen($date)>0){
			$query .= ' and left(a.ccreated_date,10)="'.$date.'" ';
		}
		if(strlen($remarks)>0){
			$query .= ' and cremarks like "%'.$remarks.'%" ';
		}
        return $this->cmysqli->result($query);
	}
	
	function get_waiting_list_by_param($data = array()){		
		$param = array();
		foreach($data as $key => $value){
			if(strtolower($key)=='ccreated_date'){
				$param[] = 'left(`ccreated_date`,10)="'.$value.'"';
			}else{
				$param[] = '`'.$key.'`="'.$value.'"';
			}			
		}
		if(sizeof($param)>0){
			$query = 'SELECT * FROM lottery_waiting_list WHERE '.implode(' AND ',$param);
			return $this->cmysqli->result($query);
		}
		return false;
	}
		
	function waiting_list_add($phone,$product,$qty=1,$type=0,$status=0,$coid=0,$remarks=''){
		$date = date("Y-m-d H:i:s");
		$query = "INSERT INTO lottery_waiting_list(`coid`,`cphone_no`,`cproduct`,`cqty`,`ctype`,`ccreated_date`,`cstatus`,`cremarks`) 
					VALUES('".$coid."','".$phone."','".$product."','".$qty."','".$type."','".$date."','".$status."','".$remarks."')";
		return $this->cmysqli->run($query);
	}
	
	function waiting_list_delete($id){		
		$query = "DELETE FROM lottery_waiting_list WHERE id='".$id."' LIMIT 1";
		return $this->cmysqli->run($query);
	}
		
	function waiting_list_update_status($id,$status){
		$query = "UPDATE lottery_waiting_list SET `cstatus`='".$status."' WHERE id='".$id."'";		
        return $this->cmysqli->run($query);
	}
	
}

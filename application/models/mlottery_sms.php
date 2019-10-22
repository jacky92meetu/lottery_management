<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mlottery_sms extends CI_Model {

    function __construct()
    {
        parent::__construct();		
    }
	
	function web_get_sms_inbox_result(){
		$query = "select * from sms_inbox order by id desc";
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	}
	
	function web_get_sms_outbox_result(){
		$query = "select * from sms_outbox order by id desc";
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	}
	
	function get_sms_processlist($all = false){
		$query = "select * from sms_processlist ";
		if($all == FALSE){
			$date = date("Y-m-d H:i:s");
			$query .= " WHERE (cstart_date<='".$date."' or cstart_date='' or cstart_date is null) ";
		}	
		$query .= " order by id";		
		return $this->cmysqli->result($query);		
	}
	
	function get_sms_processlist_limit($limit = 10){
		$query = "select * from sms_processlist ";
		$date = date("Y-m-d H:i:s");
		$query .= " WHERE (cstart_date<='".$date."' or cstart_date='' or cstart_date is null) ";
		$query .= " order by id limit ".$limit;		
		return $this->cmysqli->result($query);		
	}
	
	function save_sms_inbox($sender,$receiver,$text){
		$date = date("Y-m-d H:i:s");
		$query = "INSERT INTO sms_inbox(csender,creceiver,ctext,cstatus,cdate) 
					VALUES('".$sender."','".$receiver."','".$text."','0','".$date."')";		
        return $this->cmysqli->run($query);
	}    
	
	function save_sms_outbox($cid,$phone,$text,$senderid=''){
		$date = date("Y-m-d H:i:s");
		$query = "INSERT INTO sms_outbox(ccid,creceiver,ctext,cstatus,csender_id,cdate) 
					VALUES('".$cid."','".$phone."','".$text."','0','".$senderid."','".$date."')";		
        return $this->cmysqli->run($query);
	}
	
	function save_sms_processlist($cid,$phone,$text,$senderid='',$start_date=''){
		$date = date("Y-m-d H:i:s");
		$query = "INSERT INTO sms_processlist(ccid,cphone,ctext,csender_id,cdate,cstart_date) 
					VALUES('".$cid."','".$phone."','".$text."','".$senderid."','".$date."','".$start_date."')";		
        return $this->cmysqli->run($query);
	}
	
	function del_sms_processlist($id){		
		$query = "DELETE FROM sms_processlist WHERE id='".$id."' LIMIT 1";		
        return $this->cmysqli->run($query);
	}
	
	function mnc_add($phone){
		$date = date("Y-m-d H:i:s");
		$query = "DELETE FROM mnc_status WHERE cphone_no='".$phone."' LIMIT 1";
        $this->cmysqli->run($query);
		$query = "INSERT INTO mnc_status(`cphone_no`,`ctel_co`,`cstatus`,`ccreated_date`) VALUES('".$phone."','','0','".$date."')";
        return $this->cmysqli->run($query);
	}   
	
	function mnc_update_status($phone,$telco,$status){
		$date = date("Y-m-d H:i:s");
		$query = "UPDATE mnc_status SET `cstatus`='".$status."', `cupdated_date`='".$date."', `ctel_co`='".$telco."' WHERE cphone_no='".$phone."'";
        $this->cmysqli->run($query);
		if($status==1){
			$query = "UPDATE pmobile SET `cstatus`='1', `ctel_co`='".$telco."' WHERE cphone_no='".$phone."'";
			return $this->cmysqli->run($query);
		}
		return false;
	}
    
}

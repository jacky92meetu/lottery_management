<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CLottery_sms{
	
    function  __construct() {
		$this->CI =& get_instance();		
		$this->CI->load->model('mlottery');
		$this->CI->lang->load('sms');
	}

	
/*
 * SEND SMS
 */
	function send_bulk_sms_by_user($text){
		$this->CI->load->model('muser');
		$list = $this->CI->muser->get_user_by_param(array('group'=>'1','block'=>'0'));
		$error = 0;
				
		if(sizeof($list)>0){
			foreach($list as $value){
				$phone = $this->digit_phone($value['cmobile']);
				if($phone){
					$result = $this->sms_processlist_insert('',$phone,$text);
					if(!$result){
						$error += 1;
					}
				}				
			}
			if($error==0){				
				return true;
			}	
		}		
		return false;
	}
	
	function send_bulk_sms_by_task($text,$task){		
		$list = $this->CI->mlottery->get_pmobile_sms_list($task,1);
		$error = 0;
				
		if(sizeof($list)>0){
/*
			$telco_group = array();
			for($i=0; $i<sizeof($list); $i++){
				$value = $list[$i];				
				if($value['cstatus']=="1"){
					$telco_group[$value['ctel_co']][] = $this->digit_phone($value['cphone_no']);
				}				
			}			
			foreach($telco_group as $tel_co => $list){
				$phone = implode(",",$list);
				$result = $this->sms_processlist_insert('',$phone,$text,$tel_co);
				if(!$result){
					$error += 1;
				}
			}
*/			
			for($i=0; $i<sizeof($list); $i++){
				$value = $list[$i];				
				if($value['cstatus']=="1"){					
					$result = $this->sms_processlist_insert('',$this->digit_phone($value['cphone_no']),$text);
					if(!$result){
						$error += 1;
					}
				}				
			}
			if($error==0){				
				return true;
			}	
		}		
		return false;
	}
	
	function send_sms($cid,$phone,$text,$start_date = ''){		
		$senderid='';
		$result = $this->CI->mlottery->get_pmobile_by_phone($this->format_phone($phone));
		if($result){
			$senderid = $result[0]['ctel_co'];
		}
		$phone = $this->digit_phone($phone);
		if($phone){
			$result = $this->sms_processlist_insert($cid,$phone,$text,$senderid,$start_date);			
			if($result){
				return true;
			}
		}		
		return false;
	}
	
	function sms_processlist_insert($cid,$phone,$text,$senderid = '',$start_date = ''){
		$result = $this->CI->mlottery_sms->save_sms_processlist($cid,$phone,$text,$senderid,$start_date);
		if($result){			
			return true;
		}
		return false;
	}
	
	function sms_processlist_send($id,$cid,$phone,$text,$senderid = ''){
		$this->CI->load->library('curl');
		$senderno = '0134429611';
		$text = preg_replace("#[^\x0a\x20-\x7e]#", "", $text);
		$result = $this->CI->curl->get("http://192.168.1.178:8800/?PhoneNumber=".urlencode($phone)."&Text=".urlencode($text)."&Sender=".urlencode($senderno));
		if(stripos($result, "Message Submitted")!==FALSE){
			$this->CI->mlottery_sms->del_sms_processlist($id);
			$this->save_sms_outbox($cid,$phone,$text,$senderid);
			return true;
		}
		return false;
	}
	
	
/*
 * RESULT PROCESS
 */	
	function process_sms_auto_send($type,$date){
		$this->CI->load->library('clotteryresource');
		if($this->CI->clotteryresource->get_update_status($type,$date)){
			$this->CI->clotteryresource->set_update_status($type,$date);
			return $this->process_sms_bulk_send($type,$date);
		}
		return false;
	}
	
	function process_sms_send($type,$date = '',$phone = ''){
		$this->CI->load->library('cuser');
		$this->CI->load->library('clotteryresource');
		if(strlen($date)==0){
			$result = $this->CI->mlotteryresource->get_latest_history_by_name($type);
			if($result){
				$date = date("Y-m-d",strtotime($result[0]['cdate']));
			}
		}
		if(strlen($date)>0){
			$user = $this->CI->cuser->getLoginUser();
			$text = $this->CI->clotteryresource->sms_result_contents($type,$date);
			if($text){
				if($this->send_sms($user->id,$phone,$text)){
					return true;
				}
			}
		}
		return false;
	}
	
	function process_sms_send_by_pcode($pcode,$date,$phone = ''){
		$this->CI->load->library('cuser');
		$this->CI->load->library('clotteryresource');
		$user = $this->CI->cuser->getLoginUser();
		$product = $this->CI->mlottery->get_product($pcode, 1);
		if(isset($product[0])){
			$matches = array();
			preg_match_all("#.*\[(.+)\].*#Ui", $product[0]['cresult'], $matches);
			if(isset($matches[1]) && sizeof($matches[1])>0){
				foreach($matches[1] as $type){
					$text = $this->CI->clotteryresource->sms_result_contents($type,$date);
					if($text){
						if($this->send_sms($user->id,$phone,$text)){
							return true;
						}
					}
				}
			}
		}	
		
		return false;
	}
	
	function process_sms_bulk_send($type,$date){
		$this->CI->load->library('clotteryresource');
		$text = $this->CI->clotteryresource->sms_result_contents($type,$date);
		if($text){
			if($this->send_bulk_sms_by_task($text,$type)){
				return true;
			}
		}
		return false;
	}	

/*
 * EXTRA FUNCTION
 */
	function format_phone($phone){
		preg_match_all("#^(\+6)?(01[0-9]{8,9})$#iU", $phone, $matches);
		if(isset($matches[2][0])){
			return "+6".$matches[2][0];
		}
		return false;
	}
	
	function digit_phone($phone){
		preg_match_all("#^(\+6)?(01[0-9]{8,9})$#iU", $phone, $matches);
		if(isset($matches[2][0])){
			return $matches[2][0];
		}
		return false;
	}
	
	function mnc_check($phone){
		$telco_no = "22099";
		$phone = $this->format_phone($phone);
		$temp = $this->digit_phone($phone);
		if($temp){
			$result = $this->CI->mlottery_sms->mnc_add($phone);			
			$text = "check ".$temp;
			$result = $this->sms_processlist_insert('', $telco_no, $text);
			if($result){
				return true;
			}
		}
		return false;
	}
	
	function save_sms_inbox($sender,$recipient,$text){
		return $this->CI->mlottery_sms->save_sms_inbox($sender, $recipient, $text);
		return true;
	}
	
	function save_sms_outbox($cid,$phone,$text,$senderid=''){
		return $this->CI->mlottery_sms->save_sms_outbox($cid,$phone,$text,$senderid);
		return true;
	}
	
	function get_sms_processlist(){
		$this->CI->load->model('mlottery_sms');		
		$result = $this->CI->mlottery_sms->get_sms_processlist_limit(20);
		if($result && sizeof($result)>0){				
			foreach($result as $value){					
				$this->CI->mlottery_sms->del_sms_processlist($value['id']);					
			}			
			return json_encode($result);
		}
		return false;
	}
		
}
?>

<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CLottery_waiting_list{
	
    function  __construct() {
		$this->CI =& get_instance();	
		$this->CI->load->model('mlottery_waiting_list');
		$this->CI->load->library('clottery');
	}
	
/*
 * FUNCTION LIST
 */	
	function waiting_list_add($phone,$product,$qty=1,$type=0,$status=0,$coid=0,$remarks=''){
		if($qty>0){			
			$this->CI->load->library('clottery_sms');
			$phone = $this->CI->clottery_sms->format_phone($phone);
			if($phone){
				$p = $this->CI->mlottery->get_product($product);
				if($p && isset($p[0]['cmonth'])){
					$this->CI->load->library('ccfg');
					$setting = $this->CI->ccfg->get('mnc_check_waiting_list');
					if((bool)$setting===TRUE){
						$result = $this->CI->mlottery_waiting_list->waiting_list_add($phone,$product,$qty,$type,$status,$coid,$remarks);
						if($result){					
							return true;
						}
					}else{
						$result = $this->CI->mlottery_waiting_list->waiting_list_add($phone,$product,$qty,$type,1,$coid,$remarks);
						if($result){
							$data = $this->CI->clottery->pmobile_add($coid,$phone,$product,1,$qty);
							if($data){
								$this->CI->clottery->register_success_sms($data['cphone_no'],$data['cproduct'],$data['cexpire_date']);					
								return true;
							}
						}							
					}
				}
			}			
		}		
		return false;
	}
	
	function waiting_list_process($id){
		$result = $this->CI->mlottery_waiting_list->get_waiting_list_by_id($id);
		if($result && $result[0]['cstatus']==5){
			$data = $this->CI->clottery->pmobile_add($result[0]['coid'],$result[0]['cphone_no'],$result[0]['cproduct'],0,$result[0]['cqty']);
			if($data){
				$this->CI->clottery->register_success_sms($data['cphone_no'],$data['cproduct'],$data['cexpire_date']);					
				return $this->CI->mlottery_waiting_list->waiting_list_update_status($result[0]['id'],'1');					
			}
		}
		return false;
	}
	
}
?>

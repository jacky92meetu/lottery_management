<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CLottery_ajax{
	
    function  __construct() {
		$this->CI =& get_instance();	
		$this->CI->load->library('clottery');
		$this->CI->load->library('clottery_waiting_list');
	}	

/*
 * AJAX FUNCTION
 */
	function ajax_sms_processlist(){		
		$this->CI->load->library('clottery_sms');
		$data = "";
		$status = 0;
		
		$list = $this->CI->clottery_sms->get_sms_processlist();		
		//$list = file_get_contents('http://lotterysystem.flexbile.com/psms_processlist/');
		if(strlen($list)>0){
			$list = json_decode($list);
			if($list && sizeof($list)>0){
				foreach($list as $value){
					$value = (array)$value;
					if(strlen($value['cphone'])>0){
						$result = $this->CI->clottery_sms->sms_processlist_send($value['id'],$value['ccid'],$value['cphone'],$value['ctext'],$value['csender_id']);
						if($result){
							$data .= "<div>".$value['cphone']." - [".$value['ctext']."] was send at ".date('Y-m-d H:i:s')."</div>";
							$status = 1;
						}
					}
				}			
			}
		}
		
		return array('status'=>$status,'data'=> $data);
	}
	
	function ajax_check_send(){
		$this->CI->load->library('clottery_sms');
		$this->CI->load->library('ccfg');
		$engine_on = $this->CI->ccfg->get('cronjob_result_send');
		$date = date("Y-m-d");		
		$data = "";
		$status = 0;
		
		if($engine_on){
			$rlist = $this->CI->mlotteryresource->get_mresult_list();
			if($rlist && sizeof($rlist)>0){
				foreach($rlist as $value){
					if($value['cauto']==1 && $value['cstatus']==1){
						$result = $this->CI->clottery_sms->process_sms_auto_send($value['cname'],$date);
						if($result){
							$data .= "<div>".$value['cname']." was send at ".date('Y-m-d H:i:s')."</div>";
							$status = 1;							
						}
					}
				}
			}			
		}
		
		return array('status'=>$status,'data'=> $data);
	}
	
	function ajax_waiting_list_process(){		
		$this->CI->load->library('ccfg');		
		$engine_on = $this->CI->ccfg->get('cronjob_waiting_list_process');
		$date = date("Y-m-d H:i:s");		
		$data = "";
		$status = 0;
		
		//check expiration
		$this->_check_expiration();
		
//		$fdate = date("Y-m-d 00:00:00",strtotime($date));
//		$tdate = date("Y-m-d 17:00:00",strtotime($date));
//		if(strtotime($date)>=strtotime($fdate) && strtotime($date)<=strtotime($tdate)){
			if($engine_on){
				$rlist = $this->CI->mlottery_waiting_list->get_waiting_list_processing_list();
				if($rlist && sizeof($rlist)>0){
					foreach($rlist as $value){
						if($value['cstatus']==5){
							$result = $this->CI->clottery_waiting_list->waiting_list_process($value['id']);
							if($result){
								$data .= "<div>".$value['cphone_no']." - [".$value['cproduct']."] was added at ".date('Y-m-d H:i:s')."</div>";
								$status = 1;
							}						
						}
					}
				}			
			}
//		}
		
		return array('status'=>$status,'data'=> $data);
	}
	
	function ajax_mnc_check(){
		$this->CI->load->library('ccfg');
		$engine_on = $this->CI->ccfg->get('cronjob_mnc_check');
		$date = date("Y-m-d H:i:s");
		$data = "";
		$status = 0;
		
		$result = $this->CI->mlottery->get_pmobile_mnc_check_list_by_status();
		if($engine_on && $result && sizeof($result)>0){
			foreach($result as $value){
				if($value['cstatus']==7){
					$this->CI->mlottery->pmobile_update_status($value['cphone_no'],'8',$value['cproduct']);
				}else if($value['cstatus']==9){
					$this->CI->mlottery->pmobile_update_status($value['cphone_no'],'10',$value['cproduct']);
				}
				$status = 1;				
				$this->CI->load->library('clottery_sms');
				if($this->CI->clottery_sms->mnc_check($value['cphone_no'])){
					$data .= "<div>[SYSTEM] ".$value['cphone_no']." - ".$date." : CHECKING</div>";
				}else{
					$data .= "<div>[SYSTEM] ".$value['cphone_no']." - ".$date." : FAIL</div>";
				}
			}			
		}
		
		$result = $this->CI->mlottery_waiting_list->get_waiting_list_mnc_check_list();
		if($engine_on && $result && sizeof($result)>0){
			foreach($result as $value){
				$this->CI->mlottery_waiting_list->waiting_list_update_status($value['id'],'8');
				$status = 1;				
				$this->CI->load->library('clottery_sms');
				if($this->CI->clottery_sms->mnc_check($value['cphone_no'])){
					$data .= "<div>[WAITING LIST] ".$value['cphone_no']." - ".$date." : CHECKING</div>";
				}else{
					$data .= "<div>[WAITING LIST] ".$value['cphone_no']." - ".$date." : FAIL</div>";
				}
			}			
		}
		
		return array('status'=>$status,'data'=> $data);
	}
	
	function dashboard_check_status(){
		$engine_on = true;
		$date = date("Y-m-d H:i:s");
		$data = "";
		$status = 0;
		$conf_list = array(
			"cronjob_result_all_update"=>"4d2u_result_update_status",			
//			"cronjob_result_all_update"=>"live4d2u_result_update_status",
			"cronjob_result_all_update"=>"4d88_result_update_status",
//			"cronjob_result_all_update"=>"gila4d_result_update_status",
//			"cronjob_result_all_update"=>"4dresult_result_update_status",
//			"cronjob_result_all_update"=>"my4dresult_result_update_status",
//			"cronjob_result_all_update"=>"4dking_result_update_status",
//			"cronjob_result_toto_update"=>"toto_result_update_status",
//			"cronjob_result_magnum_update"=>"magnum_result_update_status",
//			"cronjob_result_damacai_update"=>"damacai_result_update_status",
			"cronjob_result_send"=>"result_sms_check_status",
			"cronjob_mnc_check"=>"result_sms_mnc_status",
			"cronjob_waiting_list_process"=>"result_waiting_list_process_status",
			"cronjob_sms_processlist"=>"sms_processlist_run"
		);
		$status_list = array(
			"4d2u_result_update_status"=>0,
//			"live4d2u_result_update_status"=>0,
			"4d88_result_update_status"=>0,
//			"gila4d_result_update_status"=>0,
//			"4dresult_result_update_status"=>0,
//			"my4dresult_result_update_status"=>0,
//			"4dking_result_update_status"=>0,
//			"toto_result_update_status"=>0,
//			"magnum_result_update_status"=>0,
//			"damacai_result_update_status"=>0,
			"result_sms_check_status"=>0,
			"result_sms_mnc_status"=>0,
			"result_waiting_list_process_status"=>0,
			"sms_processlist_run"=>0
		);		
		if($engine_on){
			$this->CI->load->library('ccfg');			
			foreach($conf_list as $key => $value){
				$temp = $this->CI->ccfg->get($key);
				if($temp=='0'){
					$status_list[$value] = '2';
				}
			}
			foreach($status_list as $key => $value){
				if($value!='2'){
					$status_list[$key] = intval($this->CI->ccfg->sys_is_timeout($key));
				}				
			}
			$data = $status_list;
			$status = 1;
		}
		
		return array('status'=>$status,'data'=> $data);
	}
	
	function _check_expiration(){
		$date = date("Y-m-d H:i:s");
		$data = "";
		$status = 0;
		
//		$fdate = date("Y-m-d 00:00:00",strtotime($date));
//		$tdate = date("Y-m-d 01:00:00",strtotime($date));
//		if(strtotime($date)>=strtotime($fdate) && strtotime($date)<=strtotime($tdate)){			
			$result = $this->CI->mlottery->get_pmobile_delete_list_by_expire(date("Y-m-d",strtotime($date)));
			if($result){
				foreach($result as $value){
					if($value['cstatus']=='1'){
						$this->CI->clottery->expired_sms($value['cphone_no'],$value['cproduct'],$value['cexpire_date']);
					}					
					$this->CI->mlottery->pmobile_del($value['cphone_no'],$value['cproduct']);
					/*
					//clear waiting list for free sms
					if(stristr($value['cproduct'], 'lottery_foc')!==FALSE){
						$this->CI->load->model('mlottery_waiting_list');
						$data = $this->CI->mlottery_waiting_list->get_waiting_list_by_pts($value['cphone_no'],'1');
						foreach($data as $value2){
							$this->CI->mlottery_waiting_list->waiting_list_delete($value2['id']);
						}						
					}
					*/
				}				
			}
			
			$status = 1;
//		}
		
		return array('status'=>$status,'data'=> $data);
	}
	
	function dashboard_check_send_status(){
		$this->CI->load->library('clotteryresource');
		$date = date("Y-m-d");
		$data = "";
		$status = 0;
		$data_array = array();
				
		$rlist = $this->CI->mlotteryresource->get_mresult_list();
		if($rlist && sizeof($rlist)>0){			
			foreach($rlist as $value){				
				$result = $this->CI->clotteryresource->get_update_status($value['cname'],$date);
				if($result && $value['cstatus']==1){
					$data_array[$value['cname']] = '1';
				}else{
					$data_array[$value['cname']] = '0';
				}
			}
		}
		if(sizeof($data_array)>0){
			$data = $data_array;
			$status = 1;
		}
		
		return array('status'=>$status,'data'=> $data);
	}
	
}
?>

<?php

ini_set('mysql.connect_timeout', 10);
ini_set('max_execution_time', 0);

require_once rtrim(dirname(__FILE__),'/').'/config/config.php';

class cronjob extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->library('ccfg');
		$this->load->library('clottery');
		$this->load->library('clottery_ajax');
		$this->load->library('clottery_resource/clottery4d88xpath');
	}
	
	function init(){
		/*
		if($this->ccfg->sys_is_timeout('result_waiting_list_process_status')!==TRUE){
			die("Process Exists!!!");
			return false;
		}
		 * 
		 */
		while(true){
			if((bool)$this->ccfg->sys_get_status("allow_looping_func")===FALSE){
				die("Process Not Allow!!!");
				return false;
			}
			//4d88 result update
			$this->ccfg->sys_set_timeout('4d88_result_update_status',30);			
			$this->clottery4d88xpath->endata();
			
			//mnc
			$this->ccfg->sys_set_timeout('result_sms_mnc_status',30);
			$this->clottery_ajax->ajax_mnc_check();

			//waiting list
			$this->ccfg->sys_set_timeout('result_waiting_list_process_status',30);
			$this->clottery_ajax->ajax_waiting_list_process();
			
			//result check & sms send
			$this->ccfg->sys_set_timeout('result_sms_check_status',30);
			$this->clottery_ajax->ajax_check_send();
						
			//sms processlist
			//disable if without sms gateway
			$this->ccfg->sys_set_timeout('sms_processlist_run',30);
			$this->clottery_ajax->ajax_sms_processlist();
			
			sleep(10);
		}
		
		return true;
	}
}

$class = new cronjob();
$class->init();

exit;

?>

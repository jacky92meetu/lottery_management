<?php

ini_set('mysql.connect_timeout', 30);
ini_set('max_execution_time', 0);

require_once rtrim(dirname(__FILE__),'/').'/config/config.php';

class cronjob extends CI_Controller {
	
	function __construct() {
		parent::__construct();		
	}
	
	function init(){
		$this->load->library('ccfg');
		$this->load->library('clottery_ajax');		
		if($this->ccfg->sys_is_timeout('linux_sms_processlist_status')!==TRUE){
			die("Process Exists!!!");
			return false;
		}
		while(true){
			if((bool)$this->ccfg->sys_get_status("allow_looping_func")===FALSE){
				die("Process Not Allow!!!");
				return false;
			}
			$this->ccfg->sys_set_timeout('linux_sms_processlist_status',70);
			
			//sms processlist
			$this->clottery_ajax->ajax_sms_processlist();			
			
			usleep(1000000);
		}
		
		return true;
	}
}

$class = new cronjob();
$class->init();

exit;

?>
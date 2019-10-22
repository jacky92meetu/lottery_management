<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Psms_processlist extends CI_Controller {

	function __construct() {
		parent::__construct();
	}
	
	function index()
	{
		$this->output->set_ajax();
		$this->load->library('cadmin');
		if($this->cadmin->check_allow_dns()){			
			$this->load->model('mlottery_sms');
			$this->load->library('ccfg');		
			$this->ccfg->sys_set_timeout('sms_processlist_run',10);
			$result = $this->mlottery_sms->get_sms_processlist_limit(20);
			if($result && sizeof($result)>0){				
				foreach($result as $value){					
					$this->mlottery_sms->del_sms_processlist($value['id']);					
				}			
				print json_encode($result);				
			}
				
		}
		exit;
	}
	
}
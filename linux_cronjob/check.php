<?php

ini_set('mysql.connect_timeout', 10);
ini_set('max_execution_time', 0);

require_once rtrim(dirname(__FILE__),'/').'/config/config.php';

class cronjob extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->library('ccfg');		
	}
	
	function display(){		
		$temp = $this->ccfg->sys_is_timeout('result_waiting_list_process_status');
		if($temp==false){
			print "valid";
		}else{
			print "expired";
		}		
	}
}

$class = new cronjob();
$class->display();

exit;

?>

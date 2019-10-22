<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

ini_set('mysql.connect_timeout', 30); 
set_time_limit(60);

class Update4d88 extends CI_Controller {

	function __construct() {
		parent::__construct();
		if(isset($_GET['p']) && $_GET['p']=="123456"){
			$this->load->library('clottery_resource/clottery4d88xpath');
			$this->clottery4d88xpath->endata();
		}else{
			show_404();
		}
		exit;
	}
	
}
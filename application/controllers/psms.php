<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('DISABLE_SESSION',1);

class Psms extends CI_Controller {

	function __construct() {
		parent::__construct();		
		$this->cpage->set_template('no_frame');
		$this->load->library('clottery_smsnow_2way');
	}
	
	function index()
	{
		set_time_limit(20);
		$this->output->set_ajax();
		$this->load->library('cadmin');
		if($this->cadmin->check_allow_dns()){
			$this->clottery_smsnow_2way->init_2way();
		}		
		exit;
	}
	
}
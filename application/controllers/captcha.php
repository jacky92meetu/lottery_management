<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Captcha extends CI_Controller {
	
	function __construct() {
		parent::__construct();
	}

	function index()
	{		
		$this->simple_captcha();
	}
	
	function simple_captcha(){
		$this->output->set_ajax();
		$this->load->library('ccoolcaptcha');
		$this->ccoolcaptcha->CreateImage();		
		exit;
	}	
}
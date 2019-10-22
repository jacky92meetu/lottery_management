<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hpost_controller_constructor {
	
	function index(){		
		$this->check_allow_dns();
	}
	
	function check_allow_dns(){
		global $class,$CI;
		if(strtolower($class)=="admin"){
			if(!$CI->cadmin->check_allow_dns()){
				//show_404();
				$CI->load->helper('url');			
				redirect('/error_404');				
				exit;
			}
		}
	}
	
}
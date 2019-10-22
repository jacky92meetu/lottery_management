<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class MY_Input extends CI_Input {
	
    function __construct()
    {
        parent::__construct();
    }

	function get_form_data($default = array()){
		$data = array();
		if(ISSET($_SESSION['post_data'])){
			$temp = unserialize($_SESSION['post_data']);			
			$_SESSION['post_data'] = null;
			if($temp!==false && $temp['status']==1){
				$data = array();	
				if(ISSET($temp['POST']) && $temp['POST']!==FALSE){
					return $temp['POST'];
				}							
			}				
		}		
		$data = array_merge($data,$default);
		return $data;
	}
}

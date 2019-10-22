<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hpre_controller {
	
	function index(){		
		$this->check_post_data();
	}
	
	function check_post_data(){
		if(defined('DISABLE_SESSION')){}
		else{
			if(session_id()==null){
				session_start();
			}
			if($_POST){			
				$temp['POST'] = $_POST;						
				$_SESSION['post_data'] = serialize($temp);
				header('location: '.$_SERVER['REQUEST_URI']);
				exit();
			}else if(ISSET($_SESSION['post_data'])){
				$temp = unserialize($_SESSION['post_data']);			
				if(!ISSET($temp['status'])){
					$temp['status'] = 1;
					$_SESSION['post_data'] = serialize($temp);
				}else{
					$_SESSION['post_data'] = null;	
				}		
			}
		}
	}
	
}
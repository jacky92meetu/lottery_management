<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
	
	function __construct() {				
		parent::__construct();				
		$this->load->library('cadmin');		
		$this->cpage->set_layout('default');
		$this->cpage->set_template('admin_frame');
		$this->cpage->set_html_title('ADMIN CONTROL PANEL');				
	}

	function index(){
        $this->cadmin->check_page_access();
		$this->load->library('clottery');
		$this->cpage->set_page_title('Control Panel');
		$this->lang->load('menu');
		$data = array();
		$data['link'] = array(
			"phone_manager"=>"/admin/page/phone/list_view",
			"user_manager"=>"/admin/page/user/list_view",
			"user_group"=>"/admin/page/user_group/list_view",
			"setting"=>"/admin/page/setting/list_view"
		);
		$data['lottery_type'] = $this->mlotteryresource->get_mresult_list();
		$this->load->view('admin/home',$data);
	}
	
	function login(){
		$user =& $this->cadmin->getLoginUser();
		if($user->id!=0){
			$this->load->helper('url');
			redirect(base_url().'admin/index');
		}		
		$this->cpage->set_template('admin_login');
		$data = $this->input->get_form_data();
		if(ISSET($data['section']) && $data['section']=="login"){
			$data['type'] = '2';
			$this->cadmin->login($data);
		}
		$user =& $this->cadmin->getLoginUser();
		if($user->id!=0){
			$this->load->helper('url');
			if(ISSET($_GET['return']) && strlen($_GET['return'])>0){				
				redirect(urldecode($_GET['return']));
			}else{
				redirect(base_url().'admin/index');
			}			
		}
		$this->load->view('admin/login');
	}
	
	function logout(){	        
		$this->cadmin->logout();			
	}
    
    function page(){
		$this->cadmin->check_page_access();
		$params = func_get_args();
		if(sizeof($params)>0){
			if(!isset($params[1])){
				$params[1] = "index";
			}
			if($this->cadmin->func_call($params[0],$params[1],array_slice($params,2))){
				return;
			}
            $this->load->library('cmessage');
			$this->cmessage->set_response_message("Call to invalid class \"".$params[0]."\".","error");
		}			
		$this->load->helper('url');
		redirect(base_url().'admin/index');
	}	
	
	
/*
 * AJAX FUNCTION
 */
	function ajax_check_status(){		
		$this->output->set_ajax();		
		$this->load->library('clottery_ajax');
		print json_encode($this->clottery_ajax->dashboard_check_status());
		exit;
	}	
	
	function ajax_check_send_status(){		
		$this->output->set_ajax();		
		$this->load->library('clottery_ajax');
		print json_encode($this->clottery_ajax->dashboard_check_send_status());
		exit;
	}	
	
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminControllerSms_setting {
	
    function __construct() {
		$this->CI =& get_instance();
		$this->CI->load->model('mlottery');
	}	
	
	function index(){		
		$this->list_view();
	}	
	
	function list_view(){		
		$this->CI->load->library('clisttemplate');		
		$this->CI->cpage->set_page_title('SMS Lottery Auto Send');
		$this->CI->cpage->set_javascript("myScript_DValidation.js");
				
		$data['data'] = $this->CI->mlotteryresource->web_get_mresult_list();
		$this->CI->clisttemplate->set_ajax_action(array('cauto','cstatus'));		
		$this->CI->clisttemplate->set_base_url(array('cauto'=>'ajax_auto','cstatus'=>'ajax_status'));
		$list = $this->CI->clisttemplate->get_list($data,"default_list_xdelete");		
		$this->CI->load->admin_view('default_list',array("list"=>$list));
	}

/*
 * AJAX FUNCTION
 */
	function ajax_auto($id){
		$this->CI->output->set_ajax();
        $result = $this->CI->mlotteryresource->get_mresult_list($id);		
		if($result[0]['cauto']==0){
			$cauto = 1;
		}else{
			$cauto = 0;
		}
		$this->CI->mlotteryresource->mresult_update_cauto($id,$cauto);
		$data = $this->CI->mlotteryresource->get_mresult_list($id);
		if($data){
			echo'<script>jQuery("#cauto_'.$id.'").html(unescape(\''. urlencode($data[0]['cauto']) .'\'))</script>';			
		}
	}
	
	function ajax_status($id){
		$this->CI->output->set_ajax();
        $result = $this->CI->mlotteryresource->get_mresult_list($id);		
		if($result[0]['cstatus']==0){
			$cstatus = 1;
		}else{
			$cstatus = 0;
		}
		$this->CI->mlotteryresource->mresult_update_cstatus($id,$cstatus);
		$data = $this->CI->mlotteryresource->get_mresult_list($id);
		if($data){
			echo'<script>jQuery("#cstatus_'.$id.'").html(unescape(\''. urlencode($data[0]['cstatus']) .'\'))</script>';			
		}
	}
	
}
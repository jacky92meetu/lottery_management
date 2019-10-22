<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminControllerSetting {
	
    function __construct() {
		$this->CI =& get_instance();		
		$this->CI->load->library('ccfg');
	}	
	
	function index(){		
		$this->list_view();
	}	
	
	function list_view(){
		$this->CI->load->library('clisttemplate');		
		$this->CI->cpage->set_page_title('Configuration List');
		$this->CI->cpage->set_javascript("myScript_DValidation.js");
				
		$data['data'] = $this->CI->mcfg->config_list();        
        $data['header_name'] = array("cname"=>"Name","cvalue"=>"Value");
		$this->CI->clisttemplate->set_ajax_action(array('edit'=>'dialog'));
		$this->CI->clisttemplate->set_base_url(array('cvalue'=>'ajax_edit','edit'=>'ajax_edit'));
		$list = $this->CI->clisttemplate->get_list($data,"default_list_xdelete");		
		$this->CI->load->admin_view('default_list',array("list"=>$list));
	}

/*
 * AJAX FUNCTION
 */
	function ajax_edit($id){
		$this->CI->output->set_ajax();   
		$cfg = $this->CI->mcfg->get_config_by_id($id);
		if($cfg){
			$data['id'] = $id;
			$data['name'] = $cfg[0]['cname'];
			$data['value'] = $cfg[0]['cvalue'];		
			echo $this->CI->load->admin_view('ajax_setting_edit',array("data"=>$data),TRUE);
			return true;
		}		
		$this->CI->load->library('cmessage');
		$this->CI->cmessage->set_response_message("error");
		echo '<script>JFunc.Popup.close(jQuery("$$owner_id"));</script>';
	}
	
	function ajax_confirm_edit($id,$value){
		$id = urldecode($_GET['id']);
		$value = urldecode($_GET['val']);
		$this->CI->output->set_ajax();   
		$cfg = $this->CI->mcfg->get_config_by_id($id);
		if($cfg){			
			$result = $this->CI->ccfg->set($cfg[0]['cname'],$value);
			if($result){				
				echo '<script>jQuery(\'tr#item_'.$id.' td:nth-child(3)\').html(unescape(\''. urlencode($value) .'\'));</script>';
				echo '<script>JFunc.Popup.close(jQuery("$$owner_id"));</script>';
				return true;
			}
		}		
		$this->CI->load->library('cmessage');
		$this->CI->cmessage->set_response_message("error");
		echo '<script>JFunc.Popup.close(jQuery("$$owner_id"));</script>';
	}
    
}
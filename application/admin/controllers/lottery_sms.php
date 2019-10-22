<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminControllerLottery_sms {
	
    function __construct() {
		$this->CI =& get_instance();		
		$this->CI->load->library('clottery');
		$this->CI->load->library('clottery_sms');
	}	
	
	function index(){		
		$this->sms_inbox_report();
	}
	
	function sms_send(){		
		$this->CI->load->library('cmessage');
		$this->CI->cpage->set_javascript("myScript_DValidation.js");
		$this->CI->cpage->set_page_title('Lottery Result Send - Send one SMS');
		$data = $this->CI->input->get_form_data();
		if(ISSET($data['section']) && $data['section']=="send"){
			$result = $this->CI->clottery_sms->process_sms_send($data['form_group'],$data['cdate'],$data['pmobile']);
			if($result){
				$this->CI->cmessage->set_response_message("SMS send to [".$data['pmobile']."] successfully.","notice",$_SERVER['REQUEST_URI']);
			}
			$this->CI->cmessage->set_response_message("SMS send fail!","error");
		}
		$group = $this->CI->clottery->show_mresult_group($data['form_group']);		
		$data['group'] = $group;
		$data = array("data"=>$data);
		$this->CI->load->admin_view('lottery_sms_send',$data);
	}
	
	function sms_bulk_send($type='',$date=''){		
		$this->CI->load->library('cmessage');
		$this->CI->cpage->set_javascript("myScript_DValidation.js");
		$this->CI->cpage->set_page_title('Lottery Result Send - Send bulk SMS');
		$data = $this->CI->input->get_form_data();
		if(strlen($data['form_group'])==0 && strlen($type)>0){
			$data['form_group'] = $type;
		}
		if(strlen($data['cdate'])==0 && strlen($date)>0){
			$data['cdate'] = $date;
		}
		if(ISSET($data['section']) && $data['section']=="send"){
			$result = $this->CI->clottery_sms->process_sms_auto_send($data['form_group'],$data['cdate']);
			if($result){			
				$this->CI->cmessage->set_response_message("SMS send to [".$data['form_group']."] successfully.","notice",$_SERVER['REQUEST_URI']);
			}
			$this->CI->cmessage->set_response_message("SMS send fail!","error");
		}
		$group = $this->CI->clottery->show_mresult_group($data['form_group']);		
		$data['group'] = $group;
		$data = array("data"=>$data);
		$this->CI->load->admin_view('lottery_sms_bulk_send',$data);
	}
	
	function sms_msg_send(){		
		$this->CI->load->library('cmessage');
		$this->CI->cpage->set_javascript("myScript_DValidation.js");
		$this->CI->cpage->set_page_title('SMS Send - One receipient');
		$data = $this->CI->input->get_form_data();
		if(ISSET($data['section']) && $data['section']=="send"){
			$result = $this->CI->clottery_sms->send_sms('0',$data['pmobile'],$data['msg']);
			if($result){
				$this->CI->cmessage->set_response_message("SMS send to [".$data['pmobile']."] successfully.","notice",$_SERVER['REQUEST_URI']);
			}
			$this->CI->cmessage->set_response_message("SMS send fail!","error");
		}		
		$data = array("data"=>$data);
		$this->CI->load->admin_view('lottery_sms_msg_send',$data);
	}
	
	function sms_msg_all_user_send(){		
		$this->CI->load->library('cmessage');
		$this->CI->cpage->set_javascript("myScript_DValidation.js");
		$this->CI->cpage->set_page_title('SMS Send - All Active User');
		$data = $this->CI->input->get_form_data();
		if(ISSET($data['section']) && $data['section']=="send"){
			$result = $this->CI->clottery_sms->send_bulk_sms_by_user($data['msg']);
			if($result){
				$this->CI->cmessage->set_response_message("SMS send to all active user successfully.","notice",$_SERVER['REQUEST_URI']);
			}
			$this->CI->cmessage->set_response_message("SMS send fail!","error");
		}		
		$data = array("data"=>$data);
		$this->CI->load->admin_view('lottery_sms_msg_all_user_send',$data);
	}
	
	function sms_msg_type_bulk_send(){
		$this->CI->load->library('cmessage');
		$this->CI->cpage->set_javascript("myScript_DValidation.js");
		$this->CI->cpage->set_page_title('SMS Send - Active Subscriber');
		$data = $this->CI->input->get_form_data();		
		if(ISSET($data['section']) && $data['section']=="send"){
			if($data['form_group']=="all"){
				$result = $this->CI->clottery_sms->send_bulk_sms_by_task($data['msg'],'');
				if($result){
					$this->CI->cmessage->set_response_message("SMS send to all active subscriber successfully.","notice",$_SERVER['REQUEST_URI']);
				}
			}else{
				$result = $this->CI->clottery_sms->send_bulk_sms_by_task($data['msg'],$data['form_group']);
				if($result){
					$this->CI->cmessage->set_response_message("SMS send to [".$data['form_group']."] successfully.","notice",$_SERVER['REQUEST_URI']);
				}
			}
			$this->CI->cmessage->set_response_message("SMS send fail!","error");
		}
		$group = $this->CI->clottery->show_mresult_group($data['form_group']);		
		$data['group'] = $group;
		$data = array("data"=>$data);
		$this->CI->load->admin_view('lottery_sms_msg_type_bulk_send',$data);
	}
	
	function sms_inbox_report(){		
		$this->CI->load->library('clisttemplate');		
		$this->CI->cpage->set_page_title('Lottery SMS Inbox Report');
		
		$data['data'] = $this->CI->mlottery_sms->web_get_sms_inbox_result();
		$list = $this->CI->clisttemplate->get_list($data,"default_list_xdelete");		
		$this->CI->load->admin_view('default_list',array("list"=>$list));
	}
	
	function sms_outbox_report(){		
		$this->CI->load->library('clisttemplate');		
		$this->CI->cpage->set_page_title('Lottery SMS Outbox Report');				
		
		$data['data'] = $this->CI->mlottery_sms->web_get_sms_outbox_result();
		$list = $this->CI->clisttemplate->get_list($data,"default_list_xdelete");		
		$this->CI->load->admin_view('default_list',array("list"=>$list));
	}
    
}
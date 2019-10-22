<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminControllerPhone {
	
    function __construct() {
		$this->CI =& get_instance();		
		$this->CI->load->library('clottery');		
	}	
	
	function index(){		
		$this->list_view();
	}
	
	function add($id = null){				
		$this->CI->cpage->set_page_title('Add New Subscription');
		$data = $this->CI->input->get_form_data();
		$data['notified'] = (ISSET($data['notified']) && $data['notified']=='on')?1:0;
		$data = array_merge(array("phone"=>"","username"=>"","qty"=>""),$data);
		if(ISSET($data['section']) && $data['section']=="add"){
			$this->CI->load->library('cuser');
			$this->CI->load->library('clottery_waiting_list');
			$this->CI->load->library('cmessage');				
			if(isset($data['username']) && strlen($data['username'])>0){
				$user = $this->CI->cuser->getUserByUsername($data['username']);
			}else{
				$user = $this->CI->cuser->getLoginUser();				
			}			
			if($user){	
				if($data['qty']>0){					
					$result = $this->CI->clottery_waiting_list->waiting_list_add($data['phone'],$data['form_group'],$data['qty'],2,0,$user->id);
					if($result){					
						if($data['notified']){
							$this->CI->clottery->register_success_sms($result['cphone_no'],$result['cproduct'],$result['cexpire_date']);						
						}
						$this->CI->cmessage->set_response_message("Subscribed successfully","notice",'/admin/page/phone/list_view');
					}else{
						$this->CI->cmessage->set_response_message("Phone subscription exists or fail to extends!","error");
					}	
				}else{
					$this->cmessage->set_response_message("Invalid quantity!","error");
				}
			}else{
				$this->CI->cmessage->set_response_message("Username not found!","error");
			}
		}else{
			$user = $this->CI->cuser->getUser($id);		
			if($user->id!=0){
				$data['username'] = $user->username;
			}
		}
		$this->CI->cpage->set_sub_menu(array("back"=>"/admin/page/phone/list_view/"));
		$group = $this->CI->mlottery->get_product('');
		$data['group'] = $group;
		$this->CI->load->admin_view('phone_add',array("data"=>$data));
	}
	
	function add_by_points($id = null){
		$this->CI->load->library('clottery_sms');
		$this->CI->cpage->set_page_title('Add New Subscription By Points');		
		$data = $this->CI->input->get_form_data();
		if(!is_numeric($data['qty'])){
			$data['qty'] = 1;
		}
		if(ISSET($data['section']) && $data['section']=="add"){
			$this->CI->load->library('cuser');			
			$this->CI->load->library('clottery_waiting_list');
			$this->CI->load->library('cmessage');
			$user = $this->CI->muser->get_user_by_username($data['username']);
			if($user){
				if($data['qty']>0){
					$p = $this->CI->mlottery->get_product($data['form_group'],"1");
					if($p){
						$cpoints = $this->CI->cuser_points->get_user_points($user[0]['id']);
						$total = $this->CI->cuser_points->get_product_points($p[0]['cname']) * $data['qty'];
						if($total<=$cpoints){
							if($this->CI->muser_points->points_add(($total*-1),"Subscription for ".$data['phone']." - ".$data['qty']." X [".$p[0]['cdesc']."]",$user[0]['id'])){
								$this->CI->clottery_waiting_list->waiting_list_add($data['phone'],$data['form_group'],$data['qty'],2,0,$user[0]['id']);
								$this->CI->cmessage->set_response_message($data['phone']." subscription processing...","notice",'/admin/page/phone/add_by_points');
							}else{
								$this->CI->cmessage->set_response_message("Phone subscription fail!","error");
							}
						}else{
							$this->CI->cmessage->set_response_message("Insufficient ".$this->CI->lang->line('default_points_name')."! Need ".$total." ".$this->CI->lang->line('default_points_name')." to proceed this transaction.","error");
						}
					}else{
						$this->CI->cmessage->set_response_message("Package not found!","error");
					}
				}else{
					$this->cmessage->set_response_message("Invalid quantity!","error");
				}
			}else{
				$this->CI->cmessage->set_response_message("Username not found!","error");
			}
		}else{			
			$user = $this->CI->cuser->getUser($id);
			$data['username'] = $user->username;
			if(strlen($user->cmobile)>0){
				$data['phone'] = $this->CI->clottery_sms->digit_phone($user->cmobile);
			}			
		}
		$group = $this->CI->mlottery->get_product('',1);
		$data['group'] = $group;
		$this->CI->load->admin_view('phone_add_by_points',array("data"=>$data));
	}
		
	function list_view(){
		$data = $this->CI->input->get_form_data();
		if(ISSET($data['section']) && $data['section']=="delete"){
			if(ISSET($data) && sizeof($data)>0){
				$data_arr = array();
				foreach($data as $key => $value){
					if(strtolower(substr($key,0,4))=="chk_"){
						$temp = substr($key,4,strlen($key));
						$data_arr[] = $temp;
					}
				}				
				$result = $this->CI->mlottery->pmobile_del($data_arr);
				if($result){
					$this->CI->load->library('cmessage');				
					$this->CI->cmessage->set_response_message("Phone delete successfully","notice",$_SERVER['REQUEST_URI']);				
				}
			}
		}
		$this->CI->load->library('clisttemplate');		
		$this->CI->cpage->set_page_title('Phone Subscription List');		
		$this->CI->cpage->set_sub_menu(array("add"=>"/admin/page/phone/add"));				
				
		$data['data'] = $this->CI->mlottery->web_get_pmobile_list_all();
        $data['header_name'] = array("cphone_no"=>"Phone Number","cproduct"=>"Product Code","cexpire_date"=>"expired on");
		$data['header_allow'] = array("cphone_no","cproduct","cdesc","username","cexpire_date","ctel_co","cstatus");		
		$this->CI->clisttemplate->set_ajax_action('cstatus');		
		$this->CI->clisttemplate->set_base_url(array("cstatus"=>"/admin/page/phone/block"));
		$list = $this->CI->clisttemplate->get_list($data);
		
		$this->CI->load->admin_view('default_list',array("list"=>$list));
	}
	
	function block($id){
		$this->CI->output->set_ajax();
		$pmobile = $this->CI->mlottery->get_pmobile_by_id($id);
		if($pmobile){
			if($pmobile[0]['cstatus']==1){
				$result = $this->CI->mlottery->pmobile_update_status($pmobile[0]['cphone_no'],'0',$pmobile[0]['cproduct']);
			}else{
				$result = $this->CI->mlottery->pmobile_update_status($pmobile[0]['cphone_no'],'1',$pmobile[0]['cproduct']);				
			}
			if($result){
				$pmobile = $this->CI->mlottery->get_pmobile_by_id($id);
				echo'<script>jQuery("#cstatus_'.$id.'").html(unescape(\''. urlencode($pmobile[0]['cstatus']) .'\'))</script>';				
			}
		}		
	}	
    
}
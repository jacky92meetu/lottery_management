<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminControllerUser {
	
    function __construct() {
		$this->CI =& get_instance();
		$this->CI->load->library('cuser');
		$this->CI->load->model('muser');
	}	
	
	function index(){		
		$this->list_view();
	}
	
	function add(){		
		$user = $this->CI->cuser->getUser();
		$this->CI->cpage->set_page_title('Add New User');
		$data = $this->CI->input->get_form_data();
		if(ISSET($data['section']) && $data['section']=="add"){			
			$data['block'] = (ISSET($data['block']) && $data['block']=='on')?1:0;
			$data['group'] = $data['form_group'];
			$result = $this->CI->cuser->user_add($data);			
			if($result){
				$this->CI->load->library('cmessage');				
				$this->CI->cmessage->set_response_message("New user added successfully","notice",'/admin/page/user/list_view');				
			}		
			$user->display_name = $data['name'];
			$user->username = $data['username'];
			$user->email = $data['email'];
			$user->cmobile = $data['cmobile'];
			$user->block = $data['block'];			
		}				
		$this->CI->cpage->set_sub_menu(array("back"=>"/admin/page/user/list_view/"));
		$data['user'] = $user;
		$group = $this->CI->cuser->show_group($data['form_group']);		
		$data['group'] = $group;
		$this->CI->load->admin_view('user_add',$data);
	}
	
	function edit($id = null){
		$user = $this->CI->cuser->getUser($id);
		$this->CI->cpage->set_page_title('Edit User');
		if($user->id==0){
			$this->CI->load->library('cmessage');
			$this->CI->cmessage->set_response_message("Invalid user id","error",'/admin/page/user/list_view');			
			return;
		}		
		$data = $this->CI->input->get_form_data();
		if(ISSET($data['section'])){
			$data['id'] = $user->id;
			if($data['section']=='details'){
				$data['block'] = (ISSET($data['block']) && $data['block']=='on')?1:0;
				$data['group'] = $data['form_group'];
				$result = $this->CI->cuser->user_update($data);
				if($result){
					$this->CI->load->library('cmessage');				
					$this->CI->cmessage->set_response_message("User detail update successfully","notice",'/admin/page/user/list_view');				
				}				
			}else if($data['section']=='password'){
				$result = $this->CI->muser->update_user_password($data);
				if($result){
					$this->CI->load->library('cmessage');				
					$this->CI->cmessage->set_response_message("User password update successfully","notice",'/admin/page/user/list_view');				
				}
			}
			$user = $this->CI->cuser->getUser($user->id);
		}
		$this->CI->cpage->set_sub_menu(array("back"=>"/admin/page/user/list_view/"));
		$data['user'] = $user;
		$group = $this->CI->cuser->show_group($user->usertype_id);
		$data['group'] = $group;		
		$referer = $this->CI->muser->get_user_referer_by_cid($user->id);
		$data['referrer'] = $referer;
		$this->CI->load->admin_view('user_edit',$data);
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
				$result = $this->CI->muser->delete_multiple_user($data_arr);
				if($result){
					$this->CI->load->library('cmessage');				
					$this->CI->cmessage->set_response_message("User delete successfully","notice",$_SERVER['REQUEST_URI']);				
				}
			}
		}
		$this->CI->load->library('clisttemplate');		
		$this->CI->cpage->set_page_title('User List');		
		$this->CI->cpage->set_sub_menu(array("add"=>"/admin/page/user/add"));		
        $this->CI->clisttemplate->set_ajax_action('block');		
		$this->CI->clisttemplate->set_base_url(array("add_phone2"=>"/admin/page/phone/add_by_points","points_view"=>"/admin/page/user/user_points_add"));
		$this->CI->clisttemplate->set_action(array('edit','add_phone2','points_view'));		
				
		$data['data'] = $this->CI->muser->web_get_user_list();        
        $data['header_name'] = array("desc"=>"Group Name");
		$list = $this->CI->clisttemplate->get_list($data);
		
		$this->CI->load->admin_view('user_list',array("list"=>$list));
	}	
	
	function user_points_details($id = null){
		$this->CI->load->library('cmessage');
		$user = $this->CI->cuser->getUser($id);		
		if($user->id==0){			
			$this->CI->cmessage->set_response_message("Invalid user id","error",'/admin/page/user/list_view');			
			return;
		}	
		$data = $this->CI->input->get_form_data();		
		$this->CI->load->library('clisttemplate');		
		$this->CI->load->model('muser_points');
		$this->CI->cpage->set_page_title('User Points Details');
		$this->CI->cpage->set_sub_menu(array("back"=>"/admin/page/user/user_points_add/".$id));
		$data['data'] = $this->CI->muser_points->web_get_points_details($id);
		$list = $this->CI->clisttemplate->get_list($data,"default_list_xdelete");		
		$this->CI->load->admin_view('default_list',array("list"=>$list));
	}
	
	function user_points_add($id = null){	
		$this->CI->load->library('cmessage');
		$user = $this->CI->cuser->getUser($id);		
		if($user->id==0){			
			$this->CI->cmessage->set_response_message("Invalid user id","error",'/admin/page/user/list_view');			
			return;
		}		
		$this->CI->cpage->set_page_title('User Points Management');
		$data = $this->CI->input->get_form_data();
		
		if(ISSET($data['section']) && $data['section']=="add"){
			if($this->CI->muser_points->points_add($data['points'],$data['desc'],$user->id)){
				$this->CI->cmessage->set_response_message("User points add successfully. ".$user->username." new points are ".$this->CI->cuser_points->get_display_points($user->id), "notice");
				$data = array();
			}else{
				$this->CI->cmessage->set_response_message("Fail to add user points", "error");
			}
		}		
		$this->CI->cpage->set_sub_menu(array("list"=>"/admin/page/user/user_points_details/".$id));
		
		$data['user'] = $user;
		$data['current_points'] = $this->CI->cuser_points->get_display_points($user->id);
		$this->CI->load->admin_view('user_points_add',array("data"=>$data));
	}
	
	function user_points_list(){		
		$this->CI->load->library('clisttemplate');		
		$this->CI->load->model('muser_points');
		$this->CI->cpage->set_page_title('User Points List');		
		$data['data'] = $this->CI->muser_points->web_get_all_user_points();
		$this->CI->clisttemplate->set_base_url(array("points_view"=>"/admin/page/user/user_points_add","points_details"=>"/admin/page/user/user_points_details"));
		$this->CI->clisttemplate->set_action(array('points_view','points_details'));		
		$list = $this->CI->clisttemplate->get_list($data,"default_list_xdelete");		
		$this->CI->load->admin_view('default_list',array("list"=>$list));
	}
	
	function guest_msg_list(){		
		$this->CI->load->library('clisttemplate');
		$this->CI->load->library('cmessage');
		$this->CI->cpage->set_page_title('Guest Message List');		
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
				$result = $this->CI->muser->user_guest_del($data_arr);
				if($result){					
					$this->CI->cmessage->set_response_message("Guest message delete successfully");
				}else{
					$this->CI->cmessage->set_response_message("Fail to delete guest message!","error");
				}
			}
		}
		$data['data'] = $this->CI->muser->web_get_user_guest_msg();
		$this->CI->clisttemplate->set_ajax_action('cemail');		
		$this->CI->clisttemplate->set_base_url(array("cemail"=>"/admin/page/user/email"));		
		$list = $this->CI->clisttemplate->get_list($data,"default_list");
		$this->CI->load->admin_view('default_list',array("list"=>$list));
	}
	
	
//AJAX FUNCTION	
	function email($id){
		$this->CI->output->set_ajax();
        $result = $this->CI->muser->get_guest_msg($id);
		if($result){
			echo'<script>window.open("https://mail.google.com/mail/?view=cm&fs=1&to='.$result[0]['cemail'].'&su=","_blank");</script>';
		}		
	}	
    
    function block($id){
		$this->CI->output->set_ajax();
        $result = $this->CI->muser->update_block($id);
		if($result[0]['RESULT']==1){
			$data = $this->CI->muser->get_user($id);
			if($data){
				echo'<script>jQuery("#block_'.$id.'").html(unescape(\''. urlencode($data[0]['block']) .'\'))</script>';				
			}			
		}else{
            echo '<script>alert("'.$this->CI->lang->line($result[0]['MSG']).'");</script>';
        }		
	}	
    
}
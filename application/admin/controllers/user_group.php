<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminControllerUser_group {
	
    function __construct() {
		$this->CI =& get_instance();
		$this->CI->load->library('cuser');
		$this->CI->load->model('muser');
	}	
	
	function index(){		
		$this->list_view();
	}
	
	function add(){		
		$group = array("desc"=>"","publish"=>0);
		$this->CI->cpage->set_page_title('Add New User Group');
		$data = $this->CI->input->get_form_data();
		if(ISSET($data['section']) && $data['section']=="add"){			
			$data['publish'] = (ISSET($data['publish']) && $data['publish']=='on')?1:0;
			$result = $this->CI->cuser->user_group_add($data);			
			if($result){
				$this->CI->load->library('cmessage');				
				$this->CI->cmessage->set_response_message("New user group added successfully","notice",'/admin/page/user_group/list_view');				
			}
			$group['desc'] = $data['desc'];			
			$group['publish'] = $data['publish'];			
		}
		$this->CI->cpage->set_sub_menu(array("back"=>"/admin/page/user/list_view/"));
		$data['group'] = $group;
		$this->CI->load->admin_view('user_group_add',$data);
	}
	
	function edit($id = null){
		$group = $this->CI->muser->get_user_group($id);		
		if($group===false){
			$this->CI->load->library('cmessage');
			$this->CI->cmessage->set_response_message("Invalid user group id","error",'/admin/page/user_group/list_view');			
			return;
		}		
		$this->CI->cpage->set_page_title('User Group Edit');
		$data = $this->CI->input->get_form_data();
		if(ISSET($data['section']) && $data['section']=="edit"){
			$data['id'] = $id;
			$data['publish'] = (ISSET($data['publish']) && $data['publish']=='on')?1:0;
			$result = $this->CI->cuser->user_group_update($data);
			if($result){
				$this->CI->load->library('cmessage');				
				$this->CI->cmessage->set_response_message("User group update successfully","notice",'/admin/page/user_group/list_view');				
			}
			$group = $this->CI->muser->get_user_group($id);
		}		
		$this->CI->cpage->set_sub_menu(array("back"=>"/admin/page/user/list_view/"));
		$data['group'] = $group[0];
		$this->CI->load->admin_view('user_group_edit',$data);
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
				$result = $this->CI->muser->delete_multiple_user_group($data_arr);
				if($result){
					$this->CI->load->library('cmessage');				
					$this->CI->cmessage->set_response_message("User group delete successfully","notice",$_SERVER['REQUEST_URI']);				
				}
			}
		}
		$this->CI->load->library('clisttemplate');		
		$this->CI->cpage->set_page_title('User Group List');		
		$this->CI->cpage->set_sub_menu(array("add"=>"/admin/page/user_group/add"));		
        $this->CI->clisttemplate->set_ajax_action('publish');
		$this->CI->clisttemplate->set_action('edit');		
				
		$data['data'] = $this->CI->muser->web_get_user_group_list();        
        $data['header_name'] = array("desc"=>"Group Name");
		$list = $this->CI->clisttemplate->get_list($data);
		
		$this->CI->load->admin_view('user_group_list',array("list"=>$list));
	}
	
	function publish($id){
		$this->CI->output->set_ajax();
        $result = $this->CI->muser->update_user_group_publish($id);
		if($result[0]['RESULT']==1){
			$data = $this->CI->muser->get_user_group($id);
			if($data){
				echo'<script>jQuery("#publish_'.$id.'").html(unescape(\''. urlencode($data[0]['publish']) .'\'))</script>';				
			}			
		}else{
            echo '<script>alert("'.$this->CI->lang->line($result[0]['MSG']).'");</script>';
        }
		echo '<script>JFunc.Popup.close(jQuery("$$owner_id"));</script>';
	}	
    
}
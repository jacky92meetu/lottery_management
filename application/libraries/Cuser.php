<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CUser{	
	var $id = 0;
	var $username = "";
	var $display_name = "";
	var $email = "";
    var $block = 0;
	var $usertype_id = 0;
	var $ip = "";
	var $created_date = "";	
	var $cmobile = "";
	var $session_name = "";

	function  __construct() {
		static $init = 0;
		$this->session_name = "user_".config_item('system_type');
        if(!defined('DISABLE_SESSION') && session_id()==null){
			session_start();
		}
		$this->CI =& get_instance();        
        $this->CI->load->model('muser');
		$this->CI->load->library('cuser_points');
		if(!$init){
			$init = 1;			
			$cuser =& $this->getLoginUser();
			$this->_set_session($cuser);
		}	
	}	
	
	function& getUser($id = null){
		if(is_null($id)){
			$id = 0;
		}		
		$cuser = new Cuser;
		$cuser->ip = $_SERVER['REMOTE_ADDR'];
		$temp = $this->CI->muser->get_user($id);
		if(isset($temp[0])){
			foreach($temp[0] as $key => $value){
				$cuser->$key = $value;
			}			
		}
		return $cuser;
	}	
	
	function& getUserByUsername($username){
		$user = $this->CI->muser->get_user_by_username($username);
		if($user){
			return $this->getUser($user[0]['id']);
		}
		return $this->getUser();
	}
	
	function& getUserByEmail($email){
		$user = $this->CI->muser->get_user_by_email($email);
		if($user){
			return $this->getUser($user[0]['id']);
		}
		return $this->getUser();
	}
	
	function check_page_access($is_return = FALSE){
		if($this->is_login()){
			return true;
		}
		$this->logout();
		if(!$is_return){
			$this->CI->load->library('cmessage');
			$this->CI->cmessage->set_response_message("The page you visited require you to login. Please login to continue.","error");
			$this->CI->load->helper('url');
			redirect(base_url().'member/login?return='.urlencode($_SERVER['REQUEST_URI']));			
		}		
		return false;
	}
	
	function is_login(){
		$user = $this->getLoginUser();
		$temp = $this->CI->muser->get_user($user->id);
		if($temp && $temp[0]['block']==0 && $user->ip==$_SERVER['REMOTE_ADDR']){
			return true;
		}
		$this->_del_session();
		return false;
	}
	
	function user_referrer_add($data = array()){
		$result = false;
		$error = 0;
		
		if(strlen($data['username'])>0){
			$user = $this->CI->muser->get_user_by_username($data['username']);
			if(!$user){
				$error += 1;
				$this->CI->cmessage->set_response_message("Username does not exists!","error");
			}
			if($error==0){
				if(strlen($data['referer'])>0){
					$referer = $this->CI->muser->get_user_by_username($data['referer']);				
					if(!$referer){
						$error += 1;
						$this->CI->cmessage->set_response_message("Referrer does not exists! You may key in the referrer you know or blank it.","error");
					}
					if($error==0 && $user && $referer){
						$temp = array(
							"ccid" => $user[0]['id'],
							"crid" => $referer[0]['id'],
							"cref_name" => $referer[0]['username']
						);
						$result = $this->CI->muser->add_user_referer($temp);
					}	
				}else{
					$result = $this->CI->muser->delete_user_referer($user[0]['id']);
				}	
			}
		}
		return $result;
	}
	
	function user_add($data = array()){		
		$result = false;	
		$error = 0;
		$this->CI->load->library('cmessage');
		$this->CI->load->library('clottery_sms');
		$this->CI->load->helper('email');			
		$check = preg_match("#^[a-zA-Z0-9]+[a-zA-Z0-9-]*$#", $data['username']);
		if(strlen($data['username'])<=0 || !$check){
			$error += 1;
			$this->CI->cmessage->set_response_message("Invalid username","error");
		}else if($this->CI->muser->get_user_by_username($data['username'])){
			$error += 1;
			$this->CI->cmessage->set_response_message("Username already exists","error");
		}
		if(strlen($data['password'])<=0 || ($data['password']!=$data['password2'])){
			$error += 1;
			$this->CI->cmessage->set_response_message("Password not match!","error");
		}
		if(strlen($data['email'])>0){
			if(strlen($data['email'])<=0 || !valid_email($data['email'])){
				$error += 1;
				$this->CI->cmessage->set_response_message("Invalid email address","error");
			}else if($this->CI->muser->get_user_by_email($data['email'])){
				$error += 1;
				$this->CI->cmessage->set_response_message("Email already exists","error");
			}
		}
		if(strlen($data['cmobile'])>0){
			$data['cmobile'] = $this->CI->clottery_sms->format_phone($data['cmobile']);
			if(!$data['cmobile']){
				$error += 1;
				$this->CI->cmessage->set_response_message("Mobile no format error","error");
			}else{
				$tuser = $this->CI->muser->get_user_by_cmobile($data['cmobile']);
				if($tuser && $tuser[0]['group']=="1"){
					$error += 1;
					$this->CI->cmessage->set_response_message("Mobile no exists!","error");
				}
			}
		}
		if(strlen($data['referer'])>0){
			$referer = $this->CI->muser->get_user_by_username($data['referer']);										
			if(!$referer){
				$error += 1;
				$this->CI->cmessage->set_response_message("Referrer does not exists! You may key in the referrer you know or blank it.","error");
			}
		}
		if($error==0){
			$result = $this->CI->muser->add_user($data);
			if($result){
				$user = $this->CI->muser->get_user_by_username($data['username']);
				if(strlen($data['referer'])>0){
					$this->user_referrer_add($data);
				}
				if($data['block']==1){
					$ac = $this->_create_activation_code(6);
					$this->CI->muser->update_user_by_param($user[0]['id'],array('activation_code'=>$ac));
					/*					
					$this->CI->load->library('cmailer');
					$mail_param = array('link'=>base_url().'member/activate?code='.$ac);
					$message = $this->CI->load->view('email_member_activation',array('data'=>$mail_param),true);
					$this->CI->cmailer->mail_send($user[0]['email'], config_item('site_name').' - Activation email',$message);
					 * 
					 */
				}					
			}
		}
		return $result;
	}
	
	function _create_activation_code($len = 20, $cs = TRUE){
		$words = "123456789ABCDEFGHJKLMNPQRSTUVWXYZ";
		if($cs){
			$words = "123456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";
		}		
		$result = "";
		for($i=0; $i<$len; $i++){
			$result .= substr($words, mt_rand(0, (strlen($words)-1)), 1);
		}		
		if(strlen($result)>0){
			return $result;
		}
		return false;
	}
	
	function user_group_add($data = array()){
		$result = false;	
		$error = 0;
		$this->CI->load->library('cmessage');		
		if(strlen($data['desc'])<=0){
			$error += 1;
			$this->CI->cmessage->set_response_message("Invalid group name","error");
		}else if($this->CI->muser->get_user_group_by_name($data['desc'])){
			$error += 1;
			$this->CI->cmessage->set_response_message("Group name already exists","error");
		}		
		if($error==0){
			$result = $this->CI->muser->add_user_group($data);
		}
		return $result;
	}
	
	function user_group_update($data = array()){
		$result = false;	
		$error = 0;
		$this->CI->load->library('cmessage');		
		if(strlen($data['desc'])<=0){
			$error += 1;
			$this->CI->cmessage->set_response_message("Invalid group name","error");
		}
		$group = $this->CI->muser->get_user_group($data['id']);
		if($data['desc']!=$group[0]['desc']){
			if($this->CI->muser->get_user_group_by_name($data['desc'])){
				$error += 1;
				$this->CI->cmessage->set_response_message("Group name already exists","error");
			}		
		}			
		if($error==0){
			$result = $this->CI->muser->update_user_group($data);
		}
		return $result;
	}
	
	function user_update($data = array()){
		$result = false;	
		$error = 0;
		$this->CI->load->library('cmessage');
		$this->CI->load->library('clottery_sms');
		$this->CI->load->library('cadmin');
		$this->CI->load->helper('email');
		$user = $this->getUser($data['id']);
		$admin = $this->CI->cadmin->getLoginUser();
		if($admin->username!="admin" && $user->username=="admin"){
			$error += 1;
			$this->CI->cmessage->set_response_message($this->CI->lang->line('USER_ERR2'),"error");
		}		
		if($user->email!=$data['email']){
			if(strlen($data['email'])>0 && !valid_email($data['email'])){
				$error += 1;
				$this->CI->cmessage->set_response_message("Invalid email address","error");
			}else if($this->CI->muser->get_user_by_email($data['email'])){
				$error += 1;
				$this->CI->cmessage->set_response_message("Email already exists","error");
			}
		}
		if(strlen($data['cmobile'])>0){
			$data['cmobile'] = $this->CI->clottery_sms->format_phone($data['cmobile']);
			$tuser = $this->CI->muser->get_user_by_cmobile($data['cmobile']);
			if(!$data['cmobile']){
				$error += 1;
				$this->CI->cmessage->set_response_message("Mobile no format error","error");
			}else if($tuser && $user->username!=$tuser[0]['username']){
				$error += 1;
				$this->CI->cmessage->set_response_message("Mobile no exists!","error");
			}
		}
		if(strlen($data['referer'])>0){
			$referer = $this->CI->muser->get_user_by_username($data['referer']);										
			if(!$referer){
				$error += 1;
				$this->CI->cmessage->set_response_message("Referrer does not exists! You may key in the referrer you know or blank it.","error");
			}
		}
		if($error==0){
			$id = '';
			$temp = array();
			foreach($data as $key => $value){
				if(strtolower($key)=="id"){
					$id = $value;
				}else{
					$temp[$key] = $value;
				}				
			}
			$result = $this->CI->muser->update_user_by_param($id,$temp);
			if($result){
				if(isset($data['referer'])){
					$this->user_referrer_add(array("username"=>$user->username,"referer"=>$data['referer']));
				}
			}
		}
		return $result;
	}	
	
	function show_group($id = null){
		$contents = '';
		$group = $this->CI->muser->get_user_group();
		if(isset($group[0])){			
			foreach($group as $key => $value){
				$contents .= '<OPTION value="'.$value['id'].'" ';
				if($id>0){
					if($id==$value['id']){
						$contents .= 'SELECTED';
					}					
				}else if($value['default']==1){
					$contents .= 'SELECTED';
				}
				$contents .= '>'.$value['desc'].'</OPTION>';
			}			
		}
		if(strlen($contents)>0){
			$contents = '<SELECT name="form_group">'.$contents;
			$contents = $contents.'</SELECT>';
		}
		return $contents;
	}
	
	function& getInstance($id = 0){
		static $instances = array();
		if(!is_numeric($id)){
			$id = 0;
		}		
		if(!ISSET($instances[$id])){            
			$temp = $this->CI->muser->get_user($id);
			$cuser = new Cuser;
			$cuser->ip = $_SERVER['REMOTE_ADDR'];
			if(isset($temp[0])){
				foreach($temp[0] as $key => $value){
					$cuser->$key = $value;
				}			
			}				
			$instances[$id] = $cuser;
		}
		
		return $instances[$id];
	}
	
	function& getLoginUser(){
		$cuser = $this->_get_session();
		if($cuser){
			return $this->getUser($cuser);
		}
		return $this->getUser();
	}
	
	function validate_user($data = array()){
		$data = array_merge(array('username'=>'','password'=>'','type'=>'1'),$data);
		$cuser = $this->CI->muser->user_login($data);
		if($cuser){
			return $this->getUser($cuser[0]['id']);
		}
		return false;
	}
	
	function validate_activate_code($username,$code){
		if(strlen($code)>0){
			$user = $this->CI->muser->get_user_by_param(array('username'=>$username,'activation_code'=>$code,'block'=>'1'));
			if($user){
				$this->CI->muser->update_user_by_param($user[0]['id'],array('block'=>'0','activation_code'=>''));
				return $this->getUser($user[0]['id']);
			}
		}
		return false;
	}
	
	function user_password_change($data){
		$temp = array('username'=>$data['username'],'password'=>$data['opassword']);
		if($this->validate_user($temp)){
			if(strlen($data['password'])>0 && ($data['password']==$data['password2'])){
				if($this->CI->muser->update_user_password($data)){
					return true;
				}
			}
		}
		return false;
	}
	
	function login($data = array()){
		$this->CI->load->library('cmessage');
		
		$user =& $this->getUserByUsername($data['username']);
		$result = $this->CI->muser->get_user_login_fail_attempt($user->id);
		if($result && $result[0]['attempt']>=5){
			$this->CI->cmessage->set_response_message('Username blocked. Please contact us for more details','error');
			return false;
		}		
		
		$cuser = $this->validate_user($data);
		if($cuser){
			if($cuser->block==0){
				$this->_set_session($cuser);
				return true;
			}else{
				if(strlen($cuser->activation_code)>0){					
					$this->CI->cmessage->set_response_message('You must activate your account before you can login.','error');
				}else{
					$this->CI->cmessage->set_response_message('Your account was blocked.','error');
				}				
				return false;
			}
		}
        
        $this->CI->cmessage->set_response_message($this->CI->lang->line('CC FAIL TO LOGIN'),'error');
		return false;
	}
	
	function logout(){		
		$this->_del_session();
		return true;
	}
	
	function _set_session($data){		
		if(!is_object($data)){
			return false;
		}
		$data = serialize($data->id);
		$_SESSION[$this->session_name] = $data;
		return true;
	}
	
	function _get_session(){		
		if(ISSET($_SESSION[$this->session_name]) && !EMPTY($_SESSION[$this->session_name])){
			return unserialize($_SESSION[$this->session_name]);
		}		
		return false;
	}
	
	function _del_session(){		
		if(ISSET($_SESSION[$this->session_name])){
			$_SESSION[$this->session_name] = null;
		}
	}
}

?>
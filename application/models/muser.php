<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Muser extends CI_Model {

    function __construct()
    {
        parent::__construct();		
    }
	
	function web_get_user_guest_msg(){
		$query = 'SELECT a.* FROM guest_msg a order by cdate desc';
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			foreach($data as &$value){
				foreach($value as $key => &$value2){					
					$value2 = htmlentities(urldecode($value2));
				}				
			}
			return $data;
		}
		return false;
	}
	
	function web_get_user_list(){
		$query = 'SELECT a.*,c.cref_name,d.total_points FROM user a 
					left join group_type b on a.group = b.id 
					left join user_referer c on a.id=c.ccid
					left join (select a.ccid,sum(a.cpoints) total_points from user_points a group by a.ccid) d on d.ccid=a.id
				';		
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	}
	
	function web_get_user_group_list(){
		$query = 'select a.id,a.desc,a.core,a.publish,a.createddate,a.default from group_type a';		
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	}
	
	function web_get_user_downline($id){		
		$query = 'SELECT b.*,d.total_points FROM user_referer a 
					join user b on a.ccid=b.id 
					left join (select a.ccid,sum(a.cpoints) total_points from user_points a group by a.ccid) d on d.ccid=b.id
					WHERE crid="'.$id.'" order by created_date
				';
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	}
	
	function get_guest_msg($value = null){		
		$query = 'select a.* from guest_msg a';		
        if(!is_null($value)){
            $query .= ' WHERE a.id='.$value;
        }
		return $this->cmysqli->result($query);
	}
	
	function get_user_group($value = null){		
		$query = 'select a.id,a.desc,a.core,a.publish,a.createddate,a.default from group_type a';		
        if(!is_null($value)){
            $query .= ' WHERE a.id='.$value;
        }
		return $this->cmysqli->result($query);
	}
	
	function get_user_group_by_name($value = null){		
		$query = 'select a.id,a.desc,a.core,a.publish,a.createddate,a.default from group_type a';		
        if(!is_null($value)){
            $query .= ' WHERE a.desc="'.$value.'"';
        }
		return $this->cmysqli->result($query);
	}
    
    function get_user($value = null){		
		$query = 'SELECT a.*,a.name display_name,a.group usertype_id FROM user a';
        if(!is_null($value)){
            $query .= ' WHERE a.id='.$value;
        }
		return $this->cmysqli->result($query);
	}
	
	function get_user_referer_by_cid($id=0){
		$user = $this->get_user($id);
		if($user){
			$query = 'SELECT a.id ref_id,b.* FROM user_referer a join user b on a.crid=b.id WHERE a.ccid="'.$id.'"';
			return $this->cmysqli->result($query);
		}
		return false;
	}
	
	function get_user_by_param($data = array()){		
		$param = array();
		$this->load->database();
		$fields = $this->db->list_fields('user');
		$this->db->close();
		foreach($fields as $field){
			if(isset($data[$field])){				
				$param[] = '`'.$field.'`="'.$data[$field].'"';
			}
		}		
		if(sizeof($param)>0){
			$query = 'SELECT * FROM user WHERE '.implode(' AND ',$param);
			return $this->cmysqli->result($query);
		}
		return false;
	}
	
	function get_user_referer_by_param($data = array()){		
		$param = array();
		$this->load->database();
		$fields = $this->db->list_fields('user_referer');
		$this->db->close();
		foreach($fields as $field){
			if(isset($data[$field])){				
				$param[] = '`'.$field.'`="'.$data[$field].'"';
			}
		}
		if(sizeof($param)>0){
			$query = 'SELECT a.id ref_id,b.* FROM user_referer a join user b on a.crid=b.id WHERE '.implode(' AND ',$param);			
			return $this->cmysqli->result($query);
		}
		return false;
	}
	
	function get_user_by_cmobile($value){
		$query = 'SELECT * FROM user WHERE cmobile="'.$value.'"';        
		return $this->cmysqli->result($query);
	}
	
	function get_user_by_username($value = null){
		if(is_null($value)){
			return false;
		}
		$query = 'SELECT a.id,a.username,a.name display_name,a.email,a.block,a.group usertype_id FROM user a WHERE a.username="'.$value.'"';        
		return $this->cmysqli->result($query);
	}
	
	function get_user_by_email($value = null){
		if(is_null($value)){
			return false;
		}
		$query = 'SELECT a.id,a.username,a.name display_name,a.email,a.block,a.group usertype_id FROM user a WHERE a.email="'.$value.'"';        
		return $this->cmysqli->result($query);
	}
	
	function get_user_login_fail_attempt($cid){
		$query = 'SELECT count(*) attempt FROM user_login_status WHERE cstatus=0 AND cid="'.$cid.'"';        
		return $this->cmysqli->result($query);
	}
	
	function add_user($data = array()){
		$query = "SELECT id FROM `user` WHERE username='".$data['username']."'";
		$result = $this->cmysqli->result($query);
		if(!$result){
			$date = date("Y-m-d H:i:s");
			$query = "INSERT INTO user(`username`,`password`,`name`,`email`,`group`,`block`,`created_date`,`cmobile`) 
						VALUES('".$data['username']."','".$data['password']."','".$data['name']."','".$data['email']."','".$data['group']."','".$data['block']."','".$date."','".$data['cmobile']."');";
			return $this->cmysqli->run($query);
		}
		return false;
	}
	
	function update_user($data = array()){
		$query = "SELECT id FROM `user` WHERE id='".$data['id']."'";
		$result = $this->cmysqli->result($query);
		if($result){
			$query = "UPDATE `user` SET `name`='".$data['name']."',`username`='".$data['username']."',`email`='".$data['email']."',`group`='".$data['group']."',`cmobile`='".$data['cmobile']."' WHERE id='".$data['id']."' LIMIT 1";
			return $this->cmysqli->run($query);
		}
		return false;
	}	
	
	function update_user_by_param($id = null,$data = array()){		
		$user = $this->get_user($id);
		if($user){
			$param = array();
			$this->load->database();
			$fields = $this->db->list_fields('user');
			$this->db->close();
			foreach($fields as $field){
				if(isset($data[$field])){				
					$param[] = '`'.$field.'`="'.$data[$field].'"';
				}
			}
			if(sizeof($param)>0){
				$query = 'UPDATE user SET '.implode(',',$param).' WHERE id="'.$id.'"';
				return $this->cmysqli->run($query);
			}
		}
		return false;
	}
	
	function update_user_password($data = array()){
		$user = $this->get_user($data['id']);
		if($user){
			$query = "UPDATE user SET password='".$data['password']."' WHERE id='".$data['id']."'";
			return $this->cmysqli->run($query);
		}
		return false;
	}
	
	function delete_multiple_user($data){
		if(is_array($data)){
			$data_arr = array();
			foreach($data as $value){
				$data_arr[] = '#'.$value.'#';
			}
			$data = implode(",",$data_arr);			
			$query = "DELETE FROM user WHERE '".$data."' LIKE concat('%#',id,'#%') AND username<>'admin'";			
			return $this->cmysqli->run($query);
		}else{			
			$query = "DELETE FROM user WHERE id='".$data."'";			
			return $this->cmysqli->run($query);
		}
	}
	
	function delete_multiple_user_group($data){
		if(is_array($data)){
			$data_arr = array();
			foreach($data as $value){
				$data_arr[] = '#'.$value.'#';
			}
			$data = implode(",",$data_arr);			
			$query = "DELETE FROM group_type WHERE '".$data."' LIKE concat('%#',id,'#%') AND core<>1";			
			return $this->cmysqli->run($query);
		}else{			
			$query = "DELETE FROM group_type WHERE id='".$data."'";			
			return $this->cmysqli->run($query);
		}
	}
	
	function add_user_group($data = array()){
		$date = date('Y-m-d H:i:s');
		$group = $this->get_user_group_by_name($data['desc']);
		if($group){
			$query = "INSERT INTO group_type(desc,publish,createddate) VALUES('".$data['desc']."','".$data['publish']."','".$date."')";
			return $this->cmysqli->run($query);
		}		
		return false;		
	}
	
	function update_user_group($data = array()){
		$group = $this->get_user_group($data['id']);
		if($group){
			$query = "UPDATE group_type SET desc='".$data['desc']."',publish='".$data['publish']."' WHERE id='".$data['id']."'";
			return $this->cmysqli->run($query);
		}
		return false;
	}
    
    function user_login($data = array()){		
		$date = date('Y-m-d H:i:s');
		$user = $this->get_user_by_username($data['username']);		
		if($user){			
			$query = 'SELECT * FROM `user` WHERE `username`="'.$data['username'].'" AND `password`="'.$data['password'].'"';
			if(strlen($data['type'])>0){
				$query .= ' AND `group`="'.$data['type'].'"';
			}
			$temp = $this->cmysqli->result($query);			
			if(ISSET($temp[0]['id']) && $temp[0]['id']==$user[0]['id']){
				$this->cmysqli->run('DELETE FROM user_login_status WHERE cid="'.$user[0]['id'].'"');
				$this->cmysqli->run('INSERT INTO user_login_status(cid,cdate,cstatus) VALUES("'.$user[0]['id'].'","'.$date.'","1")');
				return $temp;
			}else{
				$this->cmysqli->run('INSERT INTO user_login_status(cid,cdate,cstatus) VALUES("'.$user[0]['id'].'","'.$date.'","0")');				
			}
		}		
		return false;
	}
    
    function update_block($id = 0){
		$user = $this->get_user($id);
		if($user && $user[0]['id']!="1" && strtolower($user[0]['username'])!="admin"){
			$publish = 0;
			if($user[0]['publish']=="0"){
				$publish = 1;
			}
			$query = "UPDATE user SET block='".$publish."' WHERE id='".$id."'";
			return $this->cmysqli->run($query);
		}
		return false;
	}
	
	function update_user_group_publish($id = 0){
		$group = $this->get_user_group($id);
		if($group){
			$publish = 0;
			if($group[0]['publish']=="0"){
				$publish = 1;
			}
			$query = "UPDATE group_type SET publish='".$publish."' WHERE id='".$id."'";
			return $this->cmysqli->run($query);
		}
		return false;		
	}
	
	function user_guest_del($data){
		if(is_array($data)){
			$data_arr = array();
			foreach($data as $value){
				$data_arr[] = '#'.$value.'#';
			}
			$data = implode(",",$data_arr);			
			$query = "DELETE FROM guest_msg WHERE '".$data."' LIKE concat('%#',id,'#%')";			
			return $this->cmysqli->run($query);
		}else{			
			$query = "DELETE FROM guest_msg WHERE id='".$data."'";			
			return $this->cmysqli->run($query);
		}		
	}   
	
	function save_guest_msg($data){
		if(!is_array($data)){
			return false;
		}
		$date = date('Y-m-d H:i:s');
		$query = 'INSERT INTO guest_msg(cid,cname,cemail,ctext,cserver,cstatus,cdate) VALUES("'.$data['cid'].'","'.$data['cname'].'","'.$data['cemail'].'","'.$data['ctext'].'","'.$data['cserver'].'",0,"'.$date.'")';		
		$data = $this->cmysqli->run($query);
		if($data){
			return $data;
		}
		return false;
	}
	
	function add_user_referer($data = array()){
		$this->delete_user_referer($data['ccid']);
		$date = date("Y-m-d H:i:s");
		$query = "INSERT INTO user_referer(`ccid`,`crid`,`cref_name`,`cdate`,`cstatus`) 
					VALUES('".$data['ccid']."','".$data['crid']."','".$data['cref_name']."','".$date."','0');";
		return $this->cmysqli->run($query);
	}
	
	function update_user_referer_status_by_cid($cid=0, $status=0){
		$user = $this->get_user($cid);
		if($user){
			$query = 'UPDATE user_referer SET cstatus="'.$status.'" WHERE ccid="'.$user[0]['id'].'"';
			return $this->cmysqli->run($query);
		}
		return false;
	}
	
	function delete_user_referer($cid=0){
		$user = $this->get_user($cid);
		if($user){
			$query = 'DELETE FROM user_referer WHERE ccid="'.$user[0]['id'].'"';
			return $this->cmysqli->run($query);
		}
		return false;
	}
    
}

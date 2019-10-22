<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Muser_points extends CI_Model {

    function __construct()
    {
        parent::__construct();		
    }
	
	function web_get_all_user_points(){
		$query = 'select b.id,b.username,b.email,a.total_points from
					(select a.ccid,sum(a.cpoints) total_points from user_points a group by a.ccid) a
					join user b on a.ccid=b.id';
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	}
	
	function web_get_points_details($cid){
		$query = 'SELECT * FROM user_points a WHERE a.ccid="'.$cid.'" order by id desc';		
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	}
	
	function get_all_user_points(){
		$query = 'select b.id,b.username,b.email,a.total_points from
					(select a.ccid,sum(a.cpoints) total_points from user_points a group by a.ccid) a
					join user b on a.ccid=b.id';
		return $this->cmysqli->result($query);
	}
	
	function get_points_details($cid){
		$query = 'SELECT * FROM user_points a WHERE a.ccid="'.$cid.'" order by id desc';		
		return $this->cmysqli->result($query);
	}
	
	function get_points_by_userid($cid){
		$query = 'SELECT sum(a.cpoints) points FROM user_points a WHERE a.ccid="'.$cid.'"';		
		return $this->cmysqli->result($query);
	}
	
	function points_add($points,$desc,$cid){
		$date = date('Y-m-d H:i:s');
		$query = 'INSERT INTO user_points(ccid,cdesc,cpoints,cdate) VALUES("'.$cid.'","'.$desc.'","'.$points.'","'.$date.'")';		
		return $this->cmysqli->run($query);
	}
	
	function points_delete_by_id($id){
		$query = 'DELETE FROM user_points WHERE id="'.$id.'" limit 1';
		return $this->cmysqli->run($query);
	}
}

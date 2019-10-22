<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mlottery extends CI_Model {

    function __construct()
    {
        parent::__construct();
		$this->load->model('mlottery_sms');
		$this->load->model('mlotteryresource');
    }
	
	function web_get_pmobile_list_all(){
		$query = "select a.id,a.cphone_no,a.cproduct,c.cdesc,a.ccid,a.cexpire_date,a.ctel_co,a.cstatus,b.username
					from pmobile a left join user b on a.ccid=b.id
					left join mproduct c on a.cproduct=c.cname";			
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	}
	
	function web_get_mproduct_list(){
		$query = 'SELECT a.* FROM mproduct a order by a.id';
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	}  
	
	function web_get_msubscribe_list(){
		$query = 'select * from (
						select a.cdate, a.cqty, b.cphone_no, b.cexpire_date, c.cname, c.ctype from msubscribe a 
						inner join pmobile b on a.cpid=b.id left join mproduct c on b.cproduct=c.cname
						union all
						select a.cdate, a.cqty, b.cphone_no, b.cexpire_date, c.cname, c.ctype from msubscribe a 
						inner join pmobile_bin b on a.cpid=b.id left join mproduct c on b.cproduct=c.cname
					) a order by a.cdate desc
				';
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	} 
	
	function web_get_msubscribe_user_summary_list(){
		$query = 'select b.id id, b.username,count(*) counting,sum(cqty) total_qty,max(ccreated_date) last_created,max(cexpire_date) last_expire from
					(select b.*,a.cqty from msubscribe a join pmobile b on a.cpid=b.id where b.id is not null
					union all
					select b.*,a.cqty from msubscribe a join pmobile_bin b on a.cpid=b.id where b.id is not null) a
					join user b on a.ccid=b.id where a.cstatus=1 group by b.username';
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	} 
	
	function web_get_msubscribe_user_details_list($id){
		$query = 'select a.* from
					(select b.*,a.cqty from msubscribe a join pmobile b on a.cpid=b.id where b.id is not null
					union all
					select b.*,a.cqty from msubscribe a join pmobile_bin b on a.cpid=b.id where b.id is not null) a
					join user b on a.ccid=b.id where a.cstatus=1 and a.ccid="'.$id.'" ORDER BY a.ccreated_date desc';
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	} 
	
	function web_get_msubscribe_list_by_c($cid){
		$query = 'select a.cphone_no,a.cexpire_date,c.cname,c.cdesc from pmobile a 
					join (select cpid,ccid,cmid from msubscribe group by cpid,ccid,cmid) b on a.id=b.cpid 
					left join mproduct c on a.cproduct=c.cname where b.ccid="'.$cid.'"';
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	} 
	
	function web_get_msubscribe_group(){
		$query = 'select a.cname,a.cdesc,b.total from mproduct a inner join (select cmid,count(*) total from msubscribe group by cmid) b on a.id=b.cmid
					order by a.id';
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	} 
	
	function get_pmobile_mnc_check_list($second = 3600){
		$date = date("Y-m-d H:i:s");
		$date = date("Y-m-d H:i:s", strtotime($date)-$second);
		$query = "select a.cphone_no from pmobile a left join mnc_status b on a.cphone_no=b.cphone_no 
					where b.ccreated_date is null OR b.ccreated_date<='".$date."' GROUP BY a.cphone_no";
		$query.=' LIMIT 10';
		return $this->cmysqli->result($query);
	}
	
	function get_pmobile_mnc_check_list_by_status(){		
		$query = "select a.* from pmobile a	where a.cstatus in (7,9) GROUP BY a.cphone_no";
		$query.=' LIMIT 10';
		return $this->cmysqli->result($query);
	}
	
	function get_pmobile_by_id($id){
		$query = "select a.id,a.cphone_no,a.cproduct,a.ccid,a.cexpire_date,a.ctel_co,a.cstatus 
					from pmobile a WHERE id='".$id."' LIMIT 1";		
        return $this->cmysqli->result($query);
	} 
	
	function get_pmobile_delete_list_by_expire($date = '',$status = ''){
		if($date==''){
			$date = date("Y-m-d");
		}
		$query = "select a.* from pmobile a WHERE a.cexpire_date<'".$date."' ";
		if(strlen($status)>0){
			$query .= " and a.cstatus='".$status."' ";
		}
		$query .= " ORDER BY a.id";
        return $this->cmysqli->result($query);
	} 
	
	function get_pmobile_by_param($param){		
		$where = array();
		if(is_array($param)){
			$this->load->database();
			$fields = $this->db->list_fields('pmobile');
			$this->db->close();
			foreach($fields as $field){
				if(isset($param[$field]) && strlen($param[$field])>0){
					$where[] = ' and '.$field.'="'.$param[$field].'"';
				}
			}	
		}
		$where = implode("",$where);		
		$query = "select * from pmobile WHERE 1=1 ".$where." ";
        return $this->cmysqli->result($query);
	} 
    
    function get_pmobile_by_phone($phone, $product = null){
		$query = "select a.* from pmobile a WHERE cphone_no='".$phone."'";		
		if(!is_null($product)){
			$query .= " AND cproduct='".$product."'";
		}
        return $this->cmysqli->result($query);
	}    
	
	function get_pmobile_sms_list($cresult='',$active=1){		
		$where = array();
		if(strlen($cresult)>0){
			$where[] = "cproduct in (select cname from mproduct where cresult like '%[".$cresult."]%')";
		}
		if($active){
			$date = date("Y-m-d");
			$where[] = "cexpire_date>='".$date."'";
			$where[] = "cstatus=1";
		}
		$query = "select cphone_no,ctel_co,cstatus from pmobile";
		if(sizeof($where)>0){
			$query .= " WHERE ".implode(" AND ",$where);
		}
		$query .= " GROUP BY cphone_no,ctel_co,cstatus";
        return $this->cmysqli->result($query);
	}  
	
	function get_product($product = '', $publish = null){
		$query = "select a.* from mproduct a where 1=1";
		if(strlen($product)>0){			
			$query .= " and cname='".$product."'";
		}		
		if(!is_null($publish)){
			$query .= " and cpublish='".$publish."'";
		}
		$query .= " ORDER BY corder";
        return $this->cmysqli->result($query);
	}
	
	function get_product_by_param($param){
		$where = array();
		if(is_array($param)){
			$this->load->database();
			$fields = $this->db->list_fields('mproduct');
			$this->db->close();
			foreach($fields as $field){
				if(isset($param[$field]) && strlen($param[$field])>0){
					$where[] = $field.'="'.$param[$field].'"';
				}
			}	
		}
		if(sizeof($where)>0){
			$where = " WHERE ".implode(" AND ",$where);
		}else{
			$where = "";
		}		
		$query = "select * from mproduct".$where;
        return $this->cmysqli->result($query);
	} 
	
	function pmobile_add($cid,$phone,$product,$expire_date,$status=0){
		$date = date("Y-m-d H:i:s");
		$query = "INSERT INTO pmobile(`cphone_no`,`cproduct`,`ccid`,`ccreated_date`,`cexpire_date`,`ctel_co`,`cstatus`) 
					VALUES('".$phone."','".$product."','".$cid."','".$date."','".$expire_date."','','".$status."')";
		return $this->cmysqli->run($query);
	}
	
	function pmobile_del($phone, $product = null){
		if(is_array($phone)){
			$data_arr = array();
			foreach($phone as $value){
				$data_arr[] = '#'.$value.'#';
			}
			$data = implode(",",$data_arr);
			$query = "INSERT INTO pmobile_bin(id,cphone_no,cproduct,ccid,ccreated_date,cexpire_date,ctel_co,cstatus) 
						select id,cphone_no,cproduct,ccid,ccreated_date,cexpire_date,ctel_co,cstatus from pmobile WHERE '".$data."' LIKE concat('%#',id,'#%')";			$this->cmysqli->run($query);
			$query = "DELETE FROM pmobile WHERE '".$data."' LIKE concat('%#',id,'#%')";			
			return $this->cmysqli->run($query);
		}else{
			$query = "INSERT INTO pmobile_bin(id,cphone_no,cproduct,ccid,ccreated_date,cexpire_date,ctel_co,cstatus) 
						select id,cphone_no,cproduct,ccid,ccreated_date,cexpire_date,ctel_co,cstatus from pmobile WHERE cphone_no='".$phone."'";
			if(!is_null($product)){
				$query .= " AND cproduct='".$product."'";
			}
			$this->cmysqli->run($query);
			$query = "DELETE FROM pmobile WHERE cphone_no='".$phone."'";
			if(!is_null($product)){
				$query .= " AND cproduct='".$product."'";
			}
			return $this->cmysqli->run($query);
		}		
	}   
	
	function pmobile_update_status($phone,$status,$product=null){
		$query = "UPDATE pmobile SET `cstatus`='".$status."' WHERE cphone_no='".$phone."'";
		if(!is_null($product)){
			$query.=" AND cproduct='".$product."'";
		}
        return $this->cmysqli->run($query);
	}
	
	function pmobile_update_expire($phone,$expire,$product=null){
		$query = "UPDATE pmobile SET `cexpire_date`='".$expire."' WHERE cphone_no='".$phone."'";
		if(!is_null($product)){
			$query.=" AND cproduct='".$product."'";
		}
        return $this->cmysqli->run($query);
	}
	
	function subscribe_set($cpid,$ccid,$cmid,$cqty){
		$date = date("Y-m-d H:i:s");
		$query = "INSERT INTO msubscribe(cpid,ccid,cmid,cqty,cdate) VALUES('".$cpid."','".$ccid."','".$cmid."','".$cqty."','".$date."')";
        return $this->cmysqli->run($query);		
	}
    
}

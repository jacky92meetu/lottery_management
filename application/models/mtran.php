<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mtran extends CI_Model {

    function __construct()
    {
        parent::__construct();		
    }	

	function get_tran_product_code_list(){		
		$query = "SELECT * FROM tran_pcode ORDER BY id";
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	}
	
	function get_tran_product_code_by_code($code){		
		$query = "SELECT * FROM tran_pcode WHERE ccode='".$code."' LIMIT 1";
		return $this->cmysqli->result($query);
	}
	
	function add_tran_product_code($code,$desc,$uqty,$uprice){
		$date = date("Y-m-d H:i:s");
		$code = strtoupper($code);
		$query = "INSERT INTO tran_pcode(ccode,cdesc,cuqty,cuprice,ccreated_date) VALUES('".$code."','".$desc."','".$uqty."','".$uprice."','".$date."')";
		return $this->cmysqli->run($query);
	}
	
	function delete_multiple_tran_product_code($data){
		if(is_array($data)){
			$data_arr = array();
			foreach($data as $value){
				$data_arr[] = '#'.$value.'#';
			}
			$data = implode(",",$data_arr);			
			$query = "DELETE FROM tran_pcode WHERE '".$data."' LIKE concat('%#',id,'#%')";
			return $this->cmysqli->run($query);
		}else{			
			$query = "DELETE FROM tran_pcode WHERE id='".$data."'";
			return $this->cmysqli->run($query);
		}
	}
    
	function get_tran_order_list(){		
		$query = "SELECT a.* FROM tran a ORDER BY a.id desc";
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	}
	
	function web_get_tran_order_by_userid($id){
		$query = "select a.*,b.cdesc FROM tran a JOIN tran_pcode b ON cast(a.ccode as binary)=cast(b.ccode as binary) WHERE a.ccid='".$id."' AND b.id is not null ORDER BY ccreated_date DESC";
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			foreach($data as &$rec){
				if($rec['cstatus']=="0"){
					$rec['cstatus_desc'] = "processing";
				}else if($rec['cstatus']=="1"){
					$rec['cstatus_desc'] = "success";
				}else if($rec['cstatus']=="9"){
					$rec['cstatus_desc'] = "refund";
				}
			}
			return $data;
		}
		return false;
	}
	
	function get_tran_order_refundable_list(){		
		$query = "SELECT a.* FROM tran a WHERE a.cstatus=1 ORDER BY a.id desc";
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	}
	
	function get_tran_order_by_id($id){
		$query = "SELECT * FROM tran WHERE id='".$id."' LIMIT 1";
		return $this->cmysqli->result($query);
	}
	
	function get_genid($name){
		$fid = "";
		$query = "SELECT fcode,flen,fnum FROM genid WHERE name='".$name."'";
		$result = $this->cmysqli->result($query);
		$query = "UPDATE genid SET fnum = (fnum + 1) WHERE fcode='".$name."'";
		$this->mysqli->run($query);
		if($result && strlen($result[0]['fcode'])>0){
			$flen = $result[0]['flen'];
			$fnum = $result[0]['fnum'];			
			if($flen>0){
				for($n=0; $n<$flen; $n++){
					$fid .= "0";
				}
			}
			$fid = strtoupper($result[0]['fcode'].substr(($fid.$fnum), ($fnum * -1)));
		}
		return $fid;
	}
	
	function add_tran_order($ccid,$code,$qty,$total_amt,$total_points,$date,$comments,$status=0){
		$code = strtoupper($code);		
		//$id = $this->get_genid("order");
		$id = $this->cmysqli->call("get_genid2","order");
		if($id && strlen($id)>0){
			$query = "INSERT INTO tran(ctran_no,ccid,ccode,cqty,ctotal_amt,ctotal_points,cstatus,cdate,ccomments) VALUES('".$id."','".$ccid."','".$code."','".$qty."','".$total_amt."','".$total_points."','".$status."','".$date."','".$comments."')";
			if($this->cmysqli->run($query)){
				$tran_id = $this->cmysqli->db->insert_id();
				$result = $this->get_tran_order_by_id($tran_id);
				return $result;
			}
		}
		return false;
	}
	
	function tran_order_refund($id){
		$query = "UPDATE tran SET cstatus=9 WHERE id='".$id."' LIMIT 1";
		return $this->cmysqli->run($query);
	}
	
}

<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mlotteryresource extends CI_Model {

    function __construct()
    {
        parent::__construct();		
    }
	
	function web_get_mresult_list(){
		$query = 'SELECT a.* FROM (SELECT a.* FROM mresult a order by a.cdesc) a';
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	} 
	
	function web_get_lottery_summary(){
		$query = "select * from (select * from update_history order by cdate desc, cfrom, ctitle) a";
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	}
	
	function web_get_lottery_summary_result($table = "3d"){
		$query = "select * from (select * from ".$table." order by cdate desc, cfrom, ctitle) a";
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	}
	
	function web_get_lottery_result_by_date(){
		$query = 'select * from (select a.cdate,c.cdesc,c.cname,case when sum(ifnull(a.cstatus,0))=0 then 1 else 0 end sms_autosend from update_history a 
					join mresult_table b on a.cfrom=b.cfrom and a.ctitle=b.ctitle
					left join mresult c on b.cmid=c.id
					group by a.cdate,c.id
					order by a.cdate desc,c.id) a
				';
		$this->load->library('cpagination');
		$query = $this->cpagination->query_limit($query);
		$data = $this->cmysqli->result($query);
		if($data){
			return $data;
		}
		return false;
	}
	
	function get_mresult_list($id = null){
		$query = 'SELECT a.* FROM mresult a ';
		if(strlen($id)>0){
			$query .= ' WHERE a.id='.$id.' ';
		}
		$query .= ' order by a.cdesc';		
        return $this->cmysqli->result($query);
	}
	
	function get_mresult_by_param($param){
		$where = array();
		if(is_array($param)){
			$this->load->database();
			$fields = $this->db->list_fields('mresult');
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
		$query = "select * from mresult".$where;
        return $this->cmysqli->result($query);
	} 
	
	function get_mresult_list_by_name($name){
		$query = 'SELECT a.* FROM mresult a WHERE a.cname="'.$name.'" LIMIT 1';		
        return $this->cmysqli->result($query);
	}
	
	function get_mresult_table_by_param($param){
		$where = array();
		if(is_array($param)){
			$this->load->database();
			$fields = $this->db->list_fields('mresult_table');
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
		$query = "select * from mresult_table".$where;
        return $this->cmysqli->result($query);
	} 
	
	function get_lottery_title_by_name($name){
		$query = "select * from (select a.* from mresult_table a join mresult b on a.cmid=b.id where b.cname='".$name."' order by corder) a";
        return $this->cmysqli->result($query);
	} 
	
	function get_lottery_result_by_table_id($table,$id){
		$query = "select '".$table."' ctable,a.* from ".$table." a where a.id='".$id."' LIMIT 1";
        return $this->cmysqli->result($query);
	} 
	
	function get_lottery_result_by_tfd($table,$from,$date){
		$query = "select '".$table."' ctable,a.* from ".$table." a where a.cfrom='".$from."' and a.cdate='".$date."' order by cprize";
        return $this->cmysqli->result($query);
	} 
	
	function get_lottery_result_by_tftd($table,$from,$title,$date){
		$query = "select '".$table."' ctable,a.* from ".$table." a where a.cfrom='".$from."' and a.ctitle='".$title."' and a.cdate='".$date."' order by cprize,cno";
        return $this->cmysqli->result($query);
	}
	
	function get_lottery_result_by_param($table,$param){
		$where = array();
		$order = array();
		$limit = "";
		if(is_array($param)){
			$this->load->database();
			$fields = $this->db->list_fields($table);
			$this->db->close();
			foreach($fields as $field){
				if(isset($param[$field]) && strlen($param[$field])>0){
					$where[] = $field.'="'.$param[$field].'"';
				}
			}
			foreach($param as $key => $value){
				if(stristr($key, 'order_')){
					$temp = substr($key,strlen('order_'));
					$order[] = $temp." ".$value;
				}
			}			
		}
		if(sizeof($where)>0){
			$where = " WHERE ".implode(" AND ",$where);
		}else{
			$where = "";
		}
		if(sizeof($order)>0){
			$order = " ORDER BY ".implode(",",$order);
		}else{
			$order = "";
		}
		if(strlen($param['limit'])>0){
			$limit = " limit ".$param['limit'];
		}
		$query = "select * from ".$table.$where.$order.$limit;
        return $this->cmysqli->result($query);
	} 
	
	function get_tableresult_by_date($table,$from,$date = ''){
		if(strlen($date)>0){
			$query = "select id,cfrom,drawid,cdate,cprize,cno from ".$table." where cdate='".$date."' and cfrom='".$from."' order by cprize";
			return $this->cmysqli->result($query);
		}
		return false;
	}  
	
	function get_history($date,$from,$title,$status=null){		
		$query='SELECT id,cdate,cfrom,ctitle,cstatus FROM update_history WHERE `cdate`="'.$date.'" AND `cfrom`="'.$from.'" AND `ctitle`="'.$title.'"';
		if(strlen($status)>0){
			$query.=' AND cstatus="'.$status.'"';
		}
        return $this->cmysqli->result($query);
	}  
	
	function get_history_by_id($id){
		$query='SELECT a.* FROM update_history a WHERE a.id="'.$id.'" LIMIT 1';		
        return $this->cmysqli->result($query);
	}
	
	function get_latest_history_by_name($name){
		$name = strtolower($name);
		$query = 'select * from (select a.cdate,c.cdesc,c.cname,case when sum(ifnull(a.cstatus,0))=0 then 1 else 0 end sms_autosend from update_history a 
					join mresult_table b on a.cfrom=b.cfrom and a.ctitle=b.ctitle
					left join mresult c on b.cmid=c.id
					where c.cname="'.$name.'"
					group by a.cdate,c.id
					order by a.cdate desc,c.id) a
					limit 1
				';
		return $this->cmysqli->result($query);
	}
	
	function history_insert($date,$from,$title,$resource=""){		
		$created_date = date("Y-m-d H:i:s");
		$query='INSERT INTO update_history(`cdate`,`cfrom`,`ctitle`,`cstatus`,`ccreated_date`,`cresource`)
								VALUES("'.$date.'","'.$from.'","'.$title.'","0","'.$created_date.'","'.$resource.'")';
        return $this->cmysqli->run($query);
	}
	
	function history_delete($id){		
		$query = "DELETE FROM update_history WHERE id='".$id."' LIMIT 1";
        return $this->cmysqli->run($query);
	}
	
	function history_update_status($id,$status){
		$query = "UPDATE update_history SET `cstatus`='".$status."' WHERE id='".$id."' LIMIT 1";
        return $this->cmysqli->run($query);
	}
	
	function result_insert($table,$from,$drawid,$date,$title,$value,$step){				
		if(empty($value)){
			return false;
		}
		
		$value_array = array();
		if(is_array($value)){			
			foreach($value as $a){
				if(strlen(trim($a))>0 && is_numeric(trim($a))){
					$value_array[] = trim($a);
				}
			}
			$value = implode(",",$value_array);
		}else if(stripos($value, ",")!==FALSE){			
			$temp = explode(",",$value);
			foreach($temp as $a){
				if(strlen(trim($a))>0 && is_numeric(trim($a))){
					$value_array[] = trim($a);
				}
			}
		}else{
			$value_array = str_split($value);
		}		
		
		if(strlen($value)<=0){
			return false;
		}		
		
		$query='SELECT id FROM '.$table.' WHERE `cfrom`="'.$from.'" AND `cdate`="'.$date.'" AND `cprize`="'.$step.'" AND `cno`="'.$value.'" AND `ctitle`="'.$title.'" limit 1';
		$result = $this->cmysqli->result($query);
		if($result){
		}else{
			$k = array();
			$v = array();
			$count = 1;
			foreach($value_array as $n){
				$k[] = '`n'.$count.'`';
				$v[] = '"'.$n.'"';
				$count += 1;
			}
			if(sizeof($k)>0){
				$k = ",".implode(",",$k);
				$v = ",".implode(",",$v);			
			}else{
				$k = "";
				$v = "";
			}			
			$query='INSERT INTO '.$table.'(`cfrom`,`drawid`,`cdate`,`cprize`,`cno`,`ctitle`'.$k.')
						VALUES("'.$from.'","'.$drawid.'","'.$date.'","'.$step.'","'.$value.'","'.$title.'"'.$v.')';
			return $this->cmysqli->run($query);			
		}
		return false;
	}
	
	function result_update($table,$id,$cfrom,$drawid,$cdate,$cprize,$cno){
		if(empty($cno) || (is_string($cno) && strlen($cno)<=0)){
			return false;
		}
		$value_array = array();
		if(is_array($cno)){			
			foreach($cno as $a){
				if(strlen(trim($a))>0 && is_numeric(trim($a))){
					$value_array[] = trim($a);
				}
			}
			$cno = implode(",",$value_array);
		}else if(stripos($cno, ",")!==FALSE){			
			$temp = explode(",",$cno);
			foreach($temp as $a){
				if(strlen(trim($a))>0 && is_numeric(trim($a))){
					$value_array[] = trim($a);
				}
			}
		}else{
			$value_array = str_split($cno);
		}
		
		$k = array();		
		$count = 1;
		foreach($value_array as $n){
			$k[] = '`n'.$count.'`="'.$n.'"';			
			$count += 1;
		}
		if(sizeof($k)>0){
			$k = ",".implode(",",$k);			
		}else{
			return false;
		}	
		$query='UPDATE '.$table.' SET cfrom="'.$cfrom.'",drawid="'.$drawid.'",cdate="'.$cdate.'",cprize="'.$cprize.'",cno="'.$cno.'"'.$k.' WHERE id="'.$id.'" LIMIT 1';
		return $this->cmysqli->run($query);			
	}
	
	function result_delete($from,$date,$title){
		if(strlen($from)>0 && strlen($date)>0 && strlen($title)>0){
			$query = 'DELETE FROM 3d WHERE `cfrom`="'.$from.'" AND `cdate`="'.$date.'" AND `ctitle`="'.$title.'"';
			$this->cmysqli->run($query);
			$query = 'DELETE FROM 4d WHERE `cfrom`="'.$from.'" AND `cdate`="'.$date.'" AND `ctitle`="'.$title.'"';
			$this->cmysqli->run($query);
			$query = 'DELETE FROM 5d WHERE `cfrom`="'.$from.'" AND `cdate`="'.$date.'" AND `ctitle`="'.$title.'"';
			$this->cmysqli->run($query);
			$query = 'DELETE FROM 6d WHERE `cfrom`="'.$from.'" AND `cdate`="'.$date.'" AND `ctitle`="'.$title.'"';
			$this->cmysqli->run($query);
			$query = 'DELETE FROM jackpot WHERE `cfrom`="'.$from.'" AND `cdate`="'.$date.'" AND `ctitle`="'.$title.'"';
			$this->cmysqli->run($query);			
			return true;
		}
		return false;
	}
	
	function mresult_update_cauto($id,$cauto){
		$query = "UPDATE mresult SET `cauto`='".$cauto."' WHERE id='".$id."' LIMIT 1";
        return $this->cmysqli->run($query);
	}
	
	function mresult_update_cstatus($id,$cstatus){
		$query = "UPDATE mresult SET `cstatus`='".$cstatus."' WHERE id='".$id."' LIMIT 1";
        return $this->cmysqli->run($query);
	}
    
}

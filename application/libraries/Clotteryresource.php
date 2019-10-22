<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * CLOTTERY RESOURCES PROCESS class
 */
class CLotteryResource{
	var $resource = "SYSTEM";	
	
	function __construct() {		
		$this->CI =& get_instance();
		$this->CI->load->model('mlottery');		
	}
	
	function sms_result_contents($type,$date){
		$result = $this->get_data($type,$date);
		if($result){
			$mresult = $this->CI->mlotteryresource->get_lottery_title_by_name($type);
			if($mresult){
				if(is_file(APPPATH.'views/clottery_sms/default_template'.EXT)){
					$text = $this->CI->load->view('clottery_sms/default_template',array("result"=>$result,"mresult"=>$mresult),TRUE);
					if(strlen($text)>0){				
						return $text;
					}
				}
			}
		}
		return false;
	}
	
	function get_update_status($type,$date){		
		$title_list = $this->CI->mlotteryresource->get_lottery_title_by_name($type);
		if($title_list){
			$title_result = array();
			$error = 0;
			foreach($title_list as $value){
				$result = $this->CI->mlotteryresource->get_history($date,$value['cfrom'],$value['ctitle'],'0');
				if($result){
					foreach($result as $data){
						$title_result[] = $data;
					}
				}else{
					$error += 1;
				}
			}
			if(sizeof($title_result)>0 && $error==0){
				return $title_result;
			}
		}		
		return false;
	}
	
	function set_update_status($type,$date){
		$result = $this->get_update_status($type, $date);
		if($result){
			foreach($result as $value){
				$this->CI->mlotteryresource->history_update_status($value['id'],'1');
			}
			return true;
		}		
		return false;
	}
	
	function get_data($type,$date){
		$title_list = $this->CI->mlotteryresource->get_lottery_title_by_name($type);
		if($title_list){
			$title_result = array();
			$error = 0;
			foreach($title_list as $value){
				$result = $this->CI->mlotteryresource->get_lottery_result_by_tftd($value['ctable'],$value['cfrom'],$value['ctitle'],$date);
				if($result){
					foreach($result as $data){
						$title_result[] = $data;
					}
				}else{
					$error += 1;
				}
			}
			if(sizeof($title_result)>0 && $error==0){
				return $title_result;
			}
		}		
		
		return false;
	}
	
	function lottery_save_result($data,$silent = false){
		$this->CI->load->library('cmessage');
		$error = 0;
		if(strlen($data['resource'])==0){
			$data['resource'] = $this->resource;
		}
		if(!$this->_check_date($data['date'])){
			if(!$silent){
				$this->CI->cmessage->set_response_message("Invalid date!","error");		
			}
			return false;
		}
		$types = $this->CI->mlotteryresource->get_lottery_title_by_name($data['lottery_type']);
		if(!$types){
			if(!$silent){
				$this->CI->cmessage->set_response_message("Invalid lottery type!","error");
			}			
			return false;
		}		
		foreach($types as $type){
			$result = $this->CI->mlotteryresource->get_history($data['date'],$type['cfrom'],$type['ctitle']);
			if($result){
				$error += 1;
				if(!$silent){					
					$this->CI->cmessage->set_response_message(strtoupper($type['cfrom'])." - ".$type['ctitle']." result(".$data['date'].") already exists!","error");					
				}
			}						
			$num_list = array();
			for($i=0; $i<$type['cnum_count']; $i++){
				$fname = $type['cfrom']."_".$type['ctitle']."_".($i+1);
				$result = preg_match("#^".$type['cdigit_regexp']."$#i", $data[$fname], $matches);
				if($result){
					if((sizeof($matches)-1)==$type['cdigit_count']){
						$num_list[] = $data[$fname];
					}else{
						$error += 1;
					}					
				}else{
					$error += 1;
				}
			}
			if(sizeof($num_list)!=$type['cnum_count']){
				$error += 1;
			}					
		}
		if($error){
			return false;
		}
		$saved = 0;
		foreach($types as $type){
			$matches = null;
			$rec = 0;
			for($i=0; $i<$type['cnum_count']; $i++){
				$fname = $type['cfrom']."_".$type['ctitle']."_".($i+1);
				$result = preg_match("#^".$type['cdigit_regexp']."$#i", $data[$fname], $matches);
				if( $result && (sizeof($matches)-1)==$type['cdigit_count'] ){
					$matches = array_slice($matches, 1);
					if($type['cprize_fixed']==1){						
						$cc = $type['cprize'];
					}else{
						$cc = ($i+1);
					}
					if($this->CI->mlotteryresource->result_insert($type['ctable'], $type['cfrom'], $data['draw'], $data['date'], $type['ctitle'], $matches, $cc)){
						$rec += 1;						
					}
				}				
			}
			if($rec==$type['cnum_count']){
				$saved += 1;
				$this->CI->mlotteryresource->history_insert($data['date'],$type['cfrom'],$type['ctitle'],$data['resource']);				
				if($saved==sizeof($types)){
					return true;
				}				
			}
		}
		return false;
	}
	
	function lottery_update_result_one($data = null,$silent = false){
		$this->CI->load->library('cmessage');
		
		if(!is_array($data)){
			$data = $_GET;
		}
		if(isset($data['silent'])){
			$silent = $data['silent'];
		}
		if(strlen($data['resource'])==0){
			$data['resource'] = $this->resource;
		}
		if(!$this->_check_date($data['date'])){
			if(!$silent){
				$this->CI->cmessage->set_response_message("Invalid date!","error");		
			}
			return false;
		}
		$pdata = $this->CI->mlotteryresource->get_lottery_result_by_table_id($data['table'],$data['id']);		
		if(!$pdata){
			if(!$silent){
				$this->CI->cmessage->set_response_message("Previous result not found!","error");
			}
			return false;
		}
		$type = $this->CI->mlotteryresource->get_mresult_table_by_param(array("cfrom"=>$data['cfrom'],"ctitle"=>$pdata[0]['ctitle'],"ctable"=>$data['table']));
		if(!$type){
			if(!$silent){
				$this->CI->cmessage->set_response_message("Invalid lottery type!","error");
			}			
			return false;
		}		
		$result = preg_match("#^".$type[0]['cdigit_regexp']."$#i", $data['cno'], $matches);
		if($result && (sizeof($matches)-1)==$type[0]['cdigit_count']){			
			$matches = array_slice($matches, 1);
			if($this->CI->mlotteryresource->result_update($data['table'],$data['id'],$data['cfrom'],$data['drawid'],$data['date'],$data['cprize'],$matches)){
				return true;
			}
		}
		
		return false;
	}
	
	function save_result($data,$silent = false){
		$this->CI->load->library('cmessage');		
		if(isset($data['ttitle']) && strlen($data['ttitle'])>0){
			$ttitle = $data['ttitle'];
		}else{
			$ttitle = $data['title'];
		}
		if(isset($data['silent'])){
			$silent = $data['silent'];
		}
		$result = $this->CI->mlotteryresource->get_history($data['date'],$data['from'],$data['title']);
		if($result){
			if(!$silent){
				$this->CI->cmessage->set_response_message(strtoupper($data['from'])." - ".$ttitle." result(".$data['date'].") already exists!","error");		
			}			
			return false;
		}
		if(isset($data['check_value']) && is_array($data['check_value'])){
			$temp = $this->_check_repeat($data['check_value']);
			if($temp){				
				if(!$silent){
					$this->CI->cmessage->set_response_message(strtoupper($data['from'])." - ".$ttitle." result(".$data['date'].") data duplicate!","error");
				}				
				return false;
			}			
		}
		if($this->_save_endata($data)){
			if(!$silent){
				$this->CI->cmessage->set_response_message(strtoupper($data['from'])." - ".$ttitle." result(".$data['date'].") add successfully!","notice");
			}			
			return true;
		}else{
			if(!$silent){
				$this->CI->cmessage->set_response_message(strtoupper($data['from'])." - ".$ttitle." result(".$data['date'].") add unsuccessfully!","error");
			}			
		}
		
		return false;
	}

/*
 * EXTRA FUNCTIONS
 */	
	function _check_data($data,$len=0,$type=1){
		$error = 0;
		if(is_array($data)){			
			foreach($data as $value){				
				if(!is_numeric($value) || !$this->_check_len($value, $len, $type)){
					$error += 1;
					break;
				}				
			}
			if($this->_check_repeat($data)){
				$error += 1;
			}
		}else{
			if(!is_numeric($data) || !$this->_check_len($data, $len, $type)){
				$error += 1;
				break;
			}
		}
		if(!$error){
			return true;			
		}
		return false;
	}
	
	function _check_len($value,$len=0,$type=1){
		if($len==0 || $len==''){
			return false;
		}		
		if($type==1 && strlen($value)==$len){
			return true;
		}else if($type==2 && strlen($value)>0 && strlen($value)<=$len){
			return true;
		}		
		return false;
	}
	
	function _filter_to_array($data,$filter=null){
		if(strlen($data)>0 && !is_null($filter)){
			$data = explode($filter,$data);
			$temp = array();
			foreach($data as $value){
				if(strlen(trim($value))>0){
					$temp[] = trim($value);
				}
			}
			return $temp;
		}		
		return $data;
	}
	
	function _check_repeat($data){
		if(is_array($data) && sizeof($data)>0){
			$temp = array_count_values($data);
			foreach($temp as $key => $value){
				if($value>1){
					return $key;
				}
			}
		}		
		return false;
	}
	
	function _check_date($date){
		if(STRLEN($date)==10 && checkdate(substr($date,5,2), substr($date,8,2), substr($date,0,4))){
			return true;
		}
		return false;
	}
	
	
/*
 * CLOTTERY PROCESS
 */	
	function _get_all_data($data){
		$result = array();
		$td = array(			
			'type' => 'jackpot',
			'len' => '2',
			'count' => '',
			'from' => '',
			'title' => '',
			'date' => '',
			'draw' => '',
			'prize' => '',			
			'value' => '',			
			'resource' => $this->resource
		);
		foreach($td as $key => &$value){
			if(ISSET($_GET[$key]) && strlen(trim(urldecode($_GET[$key])))>0){
				$value = trim(urldecode($_GET[$key]));
			}else if(ISSET($data[$key]) && strlen(trim($data[$key]))>0){
				$value = trim($data[$key]);
			}
			$result[$key] = $value;
		}		
		$td['type'] = strtolower($td['type']);
		return $result;
	}
	
	function _save_endata($td = array()){
		$td = $this->_get_all_data($td);		
		$status = 0;
		$data = "";		
		if($td['type']=="jackpot"){
			$method_name = "_get_jackpot";
			$table = "jackpot";
		}else{
			$method_name = "_get_digit";
			$table = $td['len']."d";
		}		
		if(method_exists($this, $method_name)){
			$result = $this->CI->mlotteryresource->get_history($td['date'],$td['from'],$td['title']);
			if(!$result && ISSET($td['date']) && STRLEN($td['date'])==10 && checkdate(substr($td['date'],5,2), substr($td['date'],8,2), substr($td['date'],0,4))){
				$td2 = $this->$method_name($td);
				$rec = 0;				
				if(ISSET($td2['value']) && sizeof($td2['value'])>0){
					for($i=0; $i<sizeof($td2['value']); $i++){							
						if($td['prize']==''){
							$cc = ($i+1);
						}else{
							$cc = $td['prize'];
						}
						if($this->CI->mlotteryresource->result_insert($table, $td['from'], $td['draw'], $td['date'], $td['title'], $td2['value'][$i], $cc)){
							$rec += 1;
							if($i==(sizeof($td2['value']))-1){
								$this->CI->mlotteryresource->history_insert($td['date'],$td['from'],$td['title'],$td['resource']);
							}
						}							
					}					
				}
				if($rec>0){
					return true;
				}	
			}	
		}		
		
		return false;
	}
	
	function _get_digit($td){
		$td2 = array();
		$tvalue = explode("||",$td['value']);
		if(sizeof($tvalue)!=$td['count']){
			return false;
		}
		$temp = array();
		foreach($tvalue as $value){
			if(is_numeric($value)!==FALSE && $this->_check_data($value, $td['len'], 1)){
				$temp[] = $value;
			}else{
				return false;
			}
		}
		$td2['value'] = $temp;
		
		return $td2;
	}
	
		
	function _get_jackpot($td){
		$td2 = array();
		$records = explode("||",$td['value']);
		$temp = array();		
		if(sizeof($records)>0){
			foreach($records as $num){
				$tvalue = explode(",",$num);
				if(sizeof($tvalue)!=$td['count']){
					return false;
				}			
				foreach($tvalue as $value){
					if(is_numeric($value)===FALSE || !$this->_check_data($value, $td['len'], 2)){
						return false;
					}
				}
				$temp[] = $num;
			}
		}			
		$td2['value'] = $temp;
		
		return $td2;
	}
	
}

?>

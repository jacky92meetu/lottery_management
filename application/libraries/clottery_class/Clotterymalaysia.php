<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * CLOTTERY MALAYSIA class
 */
class Clotterymalaysia{
	var $resource = "SYSTEM";	
	
	function __construct() {		
		$this->CI =& get_instance();
		$this->CI->load->library('clotteryresource');		
		$this->CI->load->library('cmessage');
	}

	function magnum_4d_save($data){
		$data['from'] = 'magnum';
		$data['resource'] = $this->resource;
		$error = 0;
		$temp = explode("-", $data['date']);
		if(!checkdate($temp[1], $temp[2], $temp[0])){
			$this->CI->cmessage->set_response_message(strtoupper($data['from'])." invalid date!","error");
			return false;
		}
		
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '4',
			"count" => '3',
			"title" => '4d',			
			"value" => $data['r_1'].'||'.$data['r_2'].'||'.$data['r_3']			
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		$td = array_merge(array(
			"type" => 'digit',
			"len" => '4',
			"count" => '10',
			"prize" => '4',
			"title" => 'special',			
			"value" => implode('||',$data['r_4'])
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '4',
			"count" => '10',
			"prize" => '5',
			"title" => 'consolation',
			"value" => implode('||',$data['r_5'])
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		if($error==0){
			return true;
		}		
		return false;
	}	
	
	function damacai_4d_save($data){
		$data['from'] = 'damacai';
		$data['resource'] = $this->resource;
		$error = 0;
		$temp = explode("-", $data['date']);
		if(!checkdate($temp[1], $temp[2], $temp[0])){
			$this->CI->cmessage->set_response_message(strtoupper($data['from'])." invalid date!","error");
			return false;
		}
		
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '3',
			"count" => '3',
			"title" => '3d',
			"value" => substr($data['r_1'],0,3).'||'.substr($data['r_2'],0,3).'||'.substr($data['r_3'],0,3)
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '4',
			"count" => '3',
			"title" => '4d',
			"value" => $data['r_1'].'||'.$data['r_2'].'||'.$data['r_3']			
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '4',
			"count" => '10',
			"prize" => '4',
			"title" => 'special',
			"value" => implode('||',$data['r_4'])
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '4',
			"count" => '10',
			"prize" => '5',
			"title" => 'consolation',
			"value" => implode('||',$data['r_5'])
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		if($error==0){
			return true;
		}		
		return false;
	}
	
	function toto_4d_save($data){
		$data['from'] = 'toto';
		$data['resource'] = $this->resource;
		$error = 0;
		$temp = explode("-", $data['date']);
		if(!checkdate($temp[1], $temp[2], $temp[0])){
			$this->CI->cmessage->set_response_message(strtoupper($data['from'])." invalid date!","error");
			return false;
		}
		
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '4',
			"count" => '3',
			"title" => '4d',
			"value" => $data['r_1'].'||'.$data['r_2'].'||'.$data['r_3']			
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '4',
			"count" => '10',
			"prize" => '4',
			"title" => 'special',
			"value" => implode('||',$data['r_4'])
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '4',
			"count" => '10',
			"prize" => '5',
			"title" => 'consolation',
			"value" => implode('||',$data['r_5'])
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		if($error==0){
			return true;
		}		
		return false;
	}
	
	function toto_5d_save($data){
		$data['from'] = 'toto';
		$data['resource'] = $this->resource;
		$error = 0;
		$temp = explode("-", $data['date']);
		if(!checkdate($temp[1], $temp[2], $temp[0])){
			$this->CI->cmessage->set_response_message(strtoupper($data['from'])." invalid date!","error");
			return false;
		}
		
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '5',
			"count" => '3',
			"title" => '5d',
			"ttitle" => 'TOTO 5D',
			"value" => implode('||',$data['r_5d'])
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		if($error==0){
			return true;
		}		
		return false;
	}
	
	function toto_6d_save($data){
		$data['from'] = 'toto';
		$data['resource'] = $this->resource;
		$error = 0;
		$temp = explode("-", $data['date']);
		if(!checkdate($temp[1], $temp[2], $temp[0])){
			$this->CI->cmessage->set_response_message(strtoupper($data['from'])." invalid date!","error");
			return false;
		}
		
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '6',
			"count" => '1',
			"title" => '6d',
			"ttitle" => 'TOTO 6D',
			"value" => $data['r_6d']
		),$data);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		if($error==0){
			return true;
		}		
		return false;
	}
	
	function toto_652_save($data){
		$data['from'] = 'toto';
		$data['resource'] = $this->resource;
		$error = 0;
		$temp = explode("-", $data['date']);
		if(!checkdate($temp[1], $temp[2], $temp[0])){
			$this->CI->cmessage->set_response_message(strtoupper($data['from'])." invalid date!","error");
			return false;
		}
		
		$td = array_merge(array(			
			"type" => 'jackpot',
			"len" => '2',
			"count" => '6',
			"title" => '652',
			"ttitle" => 'MEGA TOTO 6/52',
			"value" => $data['r_652']
		),$data);		
		$td['check_value'] = explode(',',$td['value']);
		if(sizeof($td['check_value'])>=6){
			$td['count'] = sizeof($td['check_value']);
		}
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		if($error==0){
			return true;
		}		
		return false;
	}
	
	function toto_655_save($data){
		$data['from'] = 'toto';
		$data['resource'] = $this->resource;
		$error = 0;
		$temp = explode("-", $data['date']);
		if(!checkdate($temp[1], $temp[2], $temp[0])){
			$this->CI->cmessage->set_response_message(strtoupper($data['from'])." invalid date!","error");
			return false;
		}
		
		$td = array_merge(array(			
			"type" => 'jackpot',
			"len" => '2',
			"count" => '6',
			"title" => '655',
			"ttitle" => 'POWER TOTO 6/55',
			"value" => $data['r_655']
		),$data);
		$td['check_value'] = explode(',',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		if($error==0){
			return true;
		}		
		return false;
	}
	
	function toto_658_save($data){
		$data['from'] = 'toto';
		$data['resource'] = $this->resource;
		$error = 0;
		$temp = explode("-", $data['date']);
		if(!checkdate($temp[1], $temp[2], $temp[0])){
			$this->CI->cmessage->set_response_message(strtoupper($data['from'])." invalid date!","error");
			return false;
		}
		
		$td = array_merge(array(			
			"type" => 'jackpot',
			"len" => '2',
			"count" => '6',
			"title" => '658',
			"ttitle" => 'SUPREME TOTO 6/58',
			"value" => $data['r_658']
		),$data);
		$td['check_value'] = explode(',',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		if($error==0){
			return true;
		}		
		return false;
	}
	
	function toto_all_jackpot_save($data){
		$result_1 = $this->toto_5d_save($data);
		$result_2 = $this->toto_6d_save($data);
		$result_3 = $this->toto_652_save($data);
		$result_4 = $this->toto_655_save($data);
		$result_5 = $this->toto_658_save($data);		
		
		if($result_1 && $result_2 && $result_3 && $result_4 && $result_5){
			return true;
		}
		return false;
	}
	
	function sabah_3d_save($data){
		$data['from'] = 'sabah';
		$data['resource'] = $this->resource;
		$error = 0;
		$temp = explode("-", $data['date']);
		if(!checkdate($temp[1], $temp[2], $temp[0])){
			$this->CI->cmessage->set_response_message(strtoupper($data['from'])." invalid date!","error");
			return false;
		}
		
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '3',	
			"count" => '3',		
			"title" => '3d',			
			"value" => $data['r_1'].'||'.$data['r_2'].'||'.$data['r_3']			
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		if($error==0){
			return true;
		}		
		return false;
	}	
	
	function sabah_4d_save($data){
		$data['from'] = 'sabah';
		$data['resource'] = $this->resource;
		$error = 0;
		$temp = explode("-", $data['date']);
		if(!checkdate($temp[1], $temp[2], $temp[0])){
			$this->CI->cmessage->set_response_message(strtoupper($data['from'])." invalid date!","error");
			return false;
		}
		if(isset($data['r_a1'])){
			$temp = $data;
			$temp['r_1'] = $data['r_a1'];
			$temp['r_2'] = $data['r_a2'];
			$temp['r_3'] = $data['r_a3'];
			if(!$this->sabah_3d_save($temp)){
				$error += 1;
			}
		}
				
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '4',	
			"count" => '3',		
			"title" => '4d',			
			"value" => $data['r_1'].'||'.$data['r_2'].'||'.$data['r_3']			
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		$td = array_merge(array(
			"type" => 'digit',
			"len" => '4',
			"count" => '10',
			"prize" => '4',
			"title" => 'special',			
			"value" => implode('||',$data['r_4'])
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '4',
			"count" => '10',
			"prize" => '5',
			"title" => 'consolation',
			"value" => implode('||',$data['r_5'])
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		if($error==0){
			return true;
		}		
		return false;
	}	
	
	function sandakan_4d_save($data){
		$data['from'] = 'sandakan';
		$data['resource'] = $this->resource;
		$error = 0;
		$temp = explode("-", $data['date']);
		if(!checkdate($temp[1], $temp[2], $temp[0])){
			$this->CI->cmessage->set_response_message(strtoupper($data['from'])." invalid date!","error");
			return false;
		}
		
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '4',	
			"count" => '3',		
			"title" => '4d',			
			"value" => $data['r_1'].'||'.$data['r_2'].'||'.$data['r_3']			
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		$td = array_merge(array(
			"type" => 'digit',
			"len" => '4',
			"count" => '10',
			"prize" => '4',
			"title" => 'special',			
			"value" => implode('||',$data['r_4'])
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '4',
			"count" => '10',
			"prize" => '5',
			"title" => 'consolation',
			"value" => implode('||',$data['r_5'])
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		if($error==0){
			return true;
		}		
		return false;
	}	
	
	function sweep_4d_save($data){
		$data['from'] = 'sweep';
		$data['resource'] = $this->resource;
		$error = 0;
		$temp = explode("-", $data['date']);
		if(!checkdate($temp[1], $temp[2], $temp[0])){
			$this->CI->cmessage->set_response_message(strtoupper($data['from'])." invalid date!","error");
			return false;
		}
		
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '3',
			"count" => '3',
			"title" => '3d',
			"value" => substr($data['r_1'],0,3).'||'.substr($data['r_2'],0,3).'||'.substr($data['r_3'],0,3)
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '4',
			"count" => '3',
			"title" => '4d',
			"value" => $data['r_1'].'||'.$data['r_2'].'||'.$data['r_3']			
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '4',
			"count" => '10',
			"prize" => '4',
			"title" => 'special',
			"value" => implode('||',$data['r_4'])
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '4',
			"count" => '10',
			"prize" => '5',
			"title" => 'consolation',
			"value" => implode('||',$data['r_5'])
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		if($error==0){
			return true;
		}		
		return false;
	}
	
	function sabah_645_save($data){
		$data['from'] = 'sabah';
		$data['resource'] = $this->resource;
		$error = 0;
		$temp = explode("-", $data['date']);
		if(!checkdate($temp[1], $temp[2], $temp[0])){
			$this->CI->cmessage->set_response_message(strtoupper($data['from'])." invalid date!","error");
			return false;
		}
		
		$td = array_merge(array(			
			"type" => 'jackpot',
			"len" => '2',
			"count" => '7',
			"title" => '645',
			"ttitle" => 'SABAH LOTTO',
			"value" => $data['r_645']
		),$data);
		$td['check_value'] = explode(',',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		if($error==0){
			return true;
		}		
		return false;
	}
	
	function singapore_4d_save($data){
		$data['from'] = 'singapore';
		$data['resource'] = $this->resource;
		$error = 0;
		$temp = explode("-", $data['date']);
		if(!checkdate($temp[1], $temp[2], $temp[0])){
			$this->CI->cmessage->set_response_message(strtoupper($data['from'])." invalid date!","error");
			return false;
		}
		
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '4',	
			"count" => '3',		
			"title" => '4d',			
			"value" => $data['r_1'].'||'.$data['r_2'].'||'.$data['r_3']			
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		$td = array_merge(array(
			"type" => 'digit',
			"len" => '4',
			"count" => '10',
			"prize" => '4',
			"title" => 'special',			
			"value" => implode('||',$data['r_4'])
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		$td = array_merge(array(			
			"type" => 'digit',
			"len" => '4',
			"count" => '10',
			"prize" => '5',
			"title" => 'consolation',
			"value" => implode('||',$data['r_5'])
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		if($error==0){
			return true;
		}		
		return false;
	}
	
	function singapore_645_save($data){
		$data['from'] = 'singapore';
		$data['resource'] = $this->resource;
		$error = 0;
		$temp = explode("-", $data['date']);
		if(!checkdate($temp[1], $temp[2], $temp[0])){
			$this->CI->cmessage->set_response_message(strtoupper($data['from'])." invalid date!","error");
			return false;
		}
		
		$td = array_merge(array(			
			"type" => 'jackpot',
			"len" => '2',
			"count" => '7',
			"title" => '645',
			"ttitle" => 'SINGAPORE TOTO 6/45',
			"value" => $data['r_645']
		),$data);
		$td['check_value'] = explode(',',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		if($error==0){
			return true;
		}		
		return false;
	}
	
	function damacai_6d_save($data){
		$data['from'] = 'damacai';
		$data['resource'] = $this->resource;
		$error = 0;
		$temp = explode("-", $data['date']);
		if(!checkdate($temp[1], $temp[2], $temp[0])){
			$this->CI->cmessage->set_response_message(strtoupper($data['from'])." invalid date!","error");
			return false;
		}
		
		$temp = explode(",",$data['r_1']);
		$data['r_1'] = implode(",",str_split($temp[0])).','.$temp[1];
		$temp = explode(",",$data['r_2']);
		$data['r_2'] = implode(",",str_split($temp[0])).','.$temp[1];
		$temp = explode(",",$data['r_3']);
		$data['r_3'] = implode(",",str_split($temp[0])).','.$temp[1];
		$td = array_merge(array(			
			"type" => 'jackpot',
			"len" => '2',	
			"count" => '7',		
			"title" => '6d',			
			"value" => $data['r_1'].'||'.$data['r_2'].'||'.$data['r_3']			
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		foreach($data['r_4'] as &$value){
			$value = implode(",",  str_split($value));
		}
		$td = array_merge(array(
			"type" => 'jackpot',
			"len" => '1',
			"count" => '6',
			"prize" => '4',
			"title" => '6d_special',			
			"value" => implode('||',$data['r_4'])
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		foreach($data['r_5'] as &$value){
			$value = implode(",",  str_split($value));
		}
		$td = array_merge(array(			
			"type" => 'jackpot',
			"len" => '1',
			"count" => '6',
			"prize" => '5',
			"title" => '6d_consolation',
			"value" => implode('||',$data['r_5'])
		),$data);		
		$td['check_value'] = explode('||',$td['value']);
		if(!$this->CI->clotteryresource->save_result($td)){
			$error += 1;
		}
		
		if($error==0){
			return true;
		}		
		return false;
	}
	
}

?>

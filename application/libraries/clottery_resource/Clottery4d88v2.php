<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 4D88 class version 2
 */
class Clottery4d88v2{
	
	var $weekday = array(
			"Monday" => 0,
			"Tuesday" => 0,
			"Wednesday" => 0,
			"Thursday" => 0,
			"Friday" => 0,
			"Saturday" => 0,
			"Sunday" => 0			
		);
	var $start_time = "19:30";
	var $end_time = "20:30";
	var $engine_on = false;
	var $resource = "4D88.COM";
	
	function __construct() {		
		$this->CI =& get_instance();
		$this->CI->load->library('clottery_class/clotterymalaysia');
		$this->CI->clotterymalaysia->resource = $this->resource;
		$this->CI->load->library('ccfg');
		$this->engine_on = $this->CI->ccfg->get('cronjob_result_all_update');
		$weekday['Monday'] = $this->CI->ccfg->get('result_all_update_week_monday');
		$weekday['Tuesday'] = $this->CI->ccfg->get('result_all_update_week_tuesday');
		$weekday['Wednesday'] = $this->CI->ccfg->get('result_all_update_week_wednesday');
		$weekday['Thursday'] = $this->CI->ccfg->get('result_all_update_week_thursday');
		$weekday['Friday'] = $this->CI->ccfg->get('result_all_update_week_friday');
		$weekday['Saturday'] = $this->CI->ccfg->get('result_all_update_week_saturday');
		$weekday['Sunday'] = $this->CI->ccfg->get('result_all_update_week_sunday');		
		$this->weekday = $weekday;
		$this->start_time = $this->CI->ccfg->get('result_all_update_time_start');		
		$this->end_time = $this->CI->ccfg->get('result_all_update_time_end');
	}
	
	function manual_update(){
		$batch_no = $_GET['batch_no'];
		$status = 0;
		$data = "";
		
		//get data from website
		ob_start();			
		$fp = fsockopen("apps.4d88.com", 80, $errno, $errstr, 30);
		if ($fp) {
			$out = "GET /past/result/".$batch_no." HTTP/1.1\r\n";
			$out .= "Host: apps.4d88.com\r\n";
			$out .= "Connection: Close\r\n\r\n";
			fwrite($fp, $out);
			$result = "";
			while (!feof($fp)) {
				echo fgets($fp);
			}		
			fclose($fp);
		}
		$data = ob_get_contents();
		preg_match_all('#<body[^>]*>(.*)</body>#smiU', $data, $matches);
		$data = $matches[1][0];		
		ob_end_clean();			
		if(strlen($data)){				
			preg_match_all('#<script.*</script>#smiU', $data, $matches);
			if($matches){				
				for($i=0; $i<sizeof($matches); $i++){
					$data = str_replace($matches[$i], '', $data);
				}
			}			
		}
		
		if(strlen($data)>0){
			$status = 1;
		}
		
		return json_encode(array('status'=>$status,'data'=> $data));
	}
	
	function endata(){
		$cdate = date("Y-m-d H:i:s");		
		$fdate = date(date("Y-m-d",strtotime($cdate))." ".$this->start_time);
		$tdate = date(date("Y-m-d",strtotime($cdate))." ".$this->end_time);
		$wy = date("l",strtotime($cdate));
		$batch_no = date("Y-m-d",strtotime($cdate));
		$status = 0;
		$data = "";
				
		//get data from website
		if($this->engine_on && $this->weekday[$wy]==1 && strtotime($cdate)>=strtotime($fdate) && strtotime($cdate)<=strtotime($tdate)){		
			ob_start();			
			$fp = fsockopen("www.4d88.com", 80, $errno, $errstr, 30);
			if ($fp) {
				$out = "GET / HTTP/1.1\r\n";
				$out .= "Host: www.4d88.com\r\n";
				$out .= "Connection: Close\r\n\r\n";
				fwrite($fp, $out);
				$result = "";
				while (!feof($fp)) {
					echo fgets($fp);
				}		
				fclose($fp);
			}
			$data = ob_get_contents();
			preg_match_all('#<body[^>]*>(.*)</body>#smiU', $data, $matches);
			$data = $matches[1][0];			
			ob_end_clean();			
			if(strlen($data)){				
				preg_match_all('#<script.*</script>#smiU', $data, $matches);
				if($matches){				
					for($i=0; $i<sizeof($matches); $i++){
						$data = str_replace($matches[$i], '', $data);
					}
				}			
			}
		}
		
		if(strlen($data)>0){
			$status = 1;
		}
				
		return json_encode(array('status'=>$status,'data'=> $data));
	}
	
	function save_endata(){		
		$td = array();
		foreach($_GET as $key => $value){
			$td[$key] = trim(urldecode($value));
		}
		$_GET = null;		
		$status = 0;
		$data = "";
		$method_name = "_get_".$td['from']."_".$td['title'];		
		
		if(method_exists($this, $method_name)){			
			$td = $this->$method_name($td);
			if($td){
				$status = 1;
				$data = $td['from']." --- ".$td['title']." --- ".$td['date']." --- updated on ".date("Y-m-d H:i:s");
			}	
		}		
		
		return json_encode(array('status'=>$status,'data'=> $data));
	}
	
	function _get_magnum_4d($td){
		$td['date'] = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $td['date']);		
		$td['date'] = date("Y-m-d",strtotime($td['date']));
		$td['draw'] = preg_replace("#.*([0-9]+/[0-9]+).*#smiU", "$1", $td['draw']);
		
		$td['r_4d'] = explode("||",$td['r_4d']);
		$temp = array();
		foreach($td['r_4d'] as $value){
			if(strlen($value)==4 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_4d'] = $temp;
		
		$td['r_special'] = explode("||",$td['r_special']);
		$temp = array();
		foreach($td['r_special'] as $value){			
			preg_match_all("#.*([0-9]{4}).*#i", $value, $matches);
			if(ISSET($matches[1][0]) && strlen($matches[1][0])>0){
				$temp[] = $matches[1][0];
			}
		}
		$td['r_special'] = $temp;
		
		$td['r_consolation'] = explode("||",$td['r_consolation']);
		$temp = array();
		foreach($td['r_consolation'] as $value){			
			if(strlen($value)==4 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_consolation'] = $temp;
		
		$data = array(
			'silent' => true,
			'date' => $td['date'],
			'draw'=>$td['draw'],
			'r_1' => $td['r_4d'][0],
			'r_2' => $td['r_4d'][1],
			'r_3' => $td['r_4d'][2],
			'r_4' => $td['r_special'],
			'r_5' => $td['r_consolation']
		);
		if($this->CI->clotterymalaysia->magnum_4d_save($data)){
			return $td;
		}
		return false;
	}
	
	function _get_damacai_4d($td){
		$td['date'] = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $td['date']);		
		$td['date'] = date("Y-m-d",strtotime($td['date']));
		$td['draw'] = preg_replace("#.*([0-9]+/[0-9]+).*#smiU", "$1", $td['draw']);
		
		$td['r_3d'] = array();
		$td['r_4d'] = explode("||",$td['r_4d']);
		$temp = array();
		foreach($td['r_4d'] as $value){
			if(strlen($value)==4 && is_numeric($value)!==FALSE){				
				$td['r_3d'][] = substr($value, 1);
				$temp[] = $value;
			}
		}
		$td['r_4d'] = $temp;
		
		$td['r_special'] = explode("||",$td['r_special']);
		$temp = array();
		foreach($td['r_special'] as $value){			
			if(strlen($value)==4 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_special'] = $temp;
		
		$td['r_consolation'] = explode("||",$td['r_consolation']);
		$temp = array();
		foreach($td['r_consolation'] as $value){			
			if(strlen($value)==4 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_consolation'] = $temp;
		
		$data = array(
			'silent' => true,
			'date' => $td['date'],
			'draw'=>$td['draw'],
			'r_1' => $td['r_4d'][0],
			'r_2' => $td['r_4d'][1],
			'r_3' => $td['r_4d'][2],
			'r_4' => $td['r_special'],
			'r_5' => $td['r_consolation']
		);
		if($this->CI->clotterymalaysia->damacai_4d_save($data)){
			return $td;
		}
		return false;
	}
	
	function _get_toto_4d($td){
		$td['date'] = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $td['date']);		
		$td['date'] = date("Y-m-d",strtotime($td['date']));
		$td['draw'] = preg_replace("#.*([0-9]+/[0-9]+).*#smiU", "$1", $td['draw']);
		
		$td['r_4d'] = explode("||",$td['r_4d']);
		$temp = array();
		foreach($td['r_4d'] as $value){			
			if(strlen($value)==4 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_4d'] = $temp;
		
		$td['r_special'] = explode("||",$td['r_special']);
		$temp = array();
		foreach($td['r_special'] as $value){			
			if(strlen($value)==4 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_special'] = $temp;
		
		$td['r_consolation'] = explode("||",$td['r_consolation']);
		$temp = array();
		foreach($td['r_consolation'] as $value){			
			if(strlen($value)==4 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_consolation'] = $temp;
		
		$data = array(
			'silent' => true,
			'date' => $td['date'],
			'draw'=>$td['draw'],
			'r_1' => $td['r_4d'][0],
			'r_2' => $td['r_4d'][1],
			'r_3' => $td['r_4d'][2],
			'r_4' => $td['r_special'],
			'r_5' => $td['r_consolation']
		);
		if($this->CI->clotterymalaysia->toto_4d_save($data)){
			return $td;
		}
		return false;
	}
	
	function _get_toto_5d($td){
		$td['date'] = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $td['date']);		
		$td['date'] = date("Y-m-d",strtotime($td['date']));
		$td['draw'] = preg_replace("#.*([0-9]+/[0-9]+).*#smiU", "$1", $td['draw']);
				
		$td['r_5d'] = explode("||",$td['r_5d']);
		$temp = array();
		foreach($td['r_5d'] as $value){			
			if(strlen($value)==5 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_5d'] = $temp;		
		
		$data = array(
			'silent' => true,
			'date' => $td['date'],
			'draw'=>$td['draw'],			
			'r_5d' => $td['r_5d']
		);
		if($this->CI->clotterymalaysia->toto_5d_save($data)){
			return $td;
		}
		return false;
	}
	
	function _get_toto_6d($td){
		$td['date'] = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $td['date']);		
		$td['date'] = date("Y-m-d",strtotime($td['date']));
		$td['draw'] = preg_replace("#.*([0-9]+/[0-9]+).*#smiU", "$1", $td['draw']);
				
		if(strlen($td['r_6d'])<>6 || is_numeric($td['r_6d'])===FALSE){
			$td['r_6d'] = null;			
		}
		
		$data = array(
			'silent' => true,
			'date' => $td['date'],
			'draw'=>$td['draw'],			
			'r_6d' => $td['r_6d']
		);
		if($this->CI->clotterymalaysia->toto_6d_save($data)){
			return $td;
		}
		return false;
	}
	
	function _get_toto_658($td){
		$td['date'] = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $td['date']);		
		$td['date'] = date("Y-m-d",strtotime($td['date']));
		$td['draw'] = preg_replace("#.*([0-9]+/[0-9]+).*#smiU", "$1", $td['draw']);
		
		$td['r_658'] = explode(",",$td['r_658']);
		$temp = array();
		foreach($td['r_658'] as $value){			
			if(strlen($value)>=1 && strlen($value)<=2 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_658'] = $temp;		
		
		$data = array(
			'silent' => true,
			'date' => $td['date'],
			'draw'=>$td['draw'],			
			'r_658' => implode(",",$td['r_658'])
		);
		if($this->CI->clotterymalaysia->toto_658_save($data)){
			return $td;
		}
		return false;
	}
	
	function _get_toto_655($td){
		$td['date'] = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $td['date']);		
		$td['date'] = date("Y-m-d",strtotime($td['date']));
		$td['draw'] = preg_replace("#.*([0-9]+/[0-9]+).*#smiU", "$1", $td['draw']);
		
		$td['r_655'] = explode(",",$td['r_655']);
		$temp = array();
		foreach($td['r_655'] as $value){			
			if(strlen($value)>=1 && strlen($value)<=2 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_655'] = $temp;
		
		$data = array(
			'silent' => true,
			'date' => $td['date'],
			'draw'=>$td['draw'],			
			'r_655' => implode(",",$td['r_655'])
		);
		if($this->CI->clotterymalaysia->toto_655_save($data)){
			return $td;
		}
		return false;
	}
	
	function _get_toto_652($td){
		$td['date'] = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $td['date']);		
		$td['date'] = date("Y-m-d",strtotime($td['date']));
		$td['draw'] = preg_replace("#.*([0-9]+/[0-9]+).*#smiU", "$1", $td['draw']);
		
		$td['r_652'] = explode(",",$td['r_652']);
		$temp = array();
		foreach($td['r_652'] as $value){			
			if(strlen($value)>=1 && strlen($value)<=2 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_652'] = $temp;
		
		$data = array(
			'silent' => true,
			'date' => $td['date'],
			'draw'=>$td['draw'],			
			'r_652' => implode(",",$td['r_652'])
		);
		if($this->CI->clotterymalaysia->toto_652_save($data)){
			return $td;
		}
		return false;
	}
	
	function _get_sweep_4d($td){
		$td['date'] = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $td['date']);		
		$td['date'] = date("Y-m-d",strtotime($td['date']));
		$td['draw'] = preg_replace("#.*([0-9]+/[0-9]+).*#smiU", "$1", $td['draw']);
		
		$td['r_3d'] = array();
		$td['r_4d'] = explode("||",$td['r_4d']);
		$temp = array();
		foreach($td['r_4d'] as $value){
			if(strlen($value)==4 && is_numeric($value)!==FALSE){				
				$td['r_3d'][] = substr($value, 1);
				$temp[] = $value;
			}
		}
		$td['r_4d'] = $temp;
		
		$td['r_special'] = explode("||",$td['r_special']);
		$temp = array();
		foreach($td['r_special'] as $value){			
			if(strlen($value)==4 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_special'] = $temp;
		
		$td['r_consolation'] = explode("||",$td['r_consolation']);
		$temp = array();
		foreach($td['r_consolation'] as $value){			
			if(strlen($value)==4 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_consolation'] = $temp;
		
		$data = array(
			'silent' => true,
			'date' => $td['date'],
			'draw'=>$td['draw'],
			'r_1' => $td['r_4d'][0],
			'r_2' => $td['r_4d'][1],
			'r_3' => $td['r_4d'][2],
			'r_4' => $td['r_special'],
			'r_5' => $td['r_consolation']
		);
		if($this->CI->clotterymalaysia->sweep_4d_save($data)){
			return $td;
		}
		return false;
	}
	
	function _get_sandakan_4d($td){
		$td['date'] = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $td['date']);		
		$td['date'] = date("Y-m-d",strtotime($td['date']));
		$td['draw'] = preg_replace("#.*([0-9]+/[0-9]+).*#smiU", "$1", $td['draw']);
		
		$td['r_4d'] = explode("||",$td['r_4d']);
		$temp = array();
		foreach($td['r_4d'] as $value){
			if(strlen($value)==4 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_4d'] = $temp;
		
		$td['r_special'] = explode("||",$td['r_special']);
		$temp = array();
		foreach($td['r_special'] as $value){			
			preg_match_all("#.*([0-9]{4}).*#i", $value, $matches);
			if(ISSET($matches[1][0]) && strlen($matches[1][0])>0){
				$temp[] = $matches[1][0];
			}
		}
		$td['r_special'] = $temp;
		
		$td['r_consolation'] = explode("||",$td['r_consolation']);
		$temp = array();
		foreach($td['r_consolation'] as $value){			
			if(strlen($value)==4 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_consolation'] = $temp;
		
		$data = array(
			'silent' => true,
			'date' => $td['date'],
			'draw'=>$td['draw'],
			'r_1' => $td['r_4d'][0],
			'r_2' => $td['r_4d'][1],
			'r_3' => $td['r_4d'][2],
			'r_4' => $td['r_special'],
			'r_5' => $td['r_consolation']
		);
		if($this->CI->clotterymalaysia->sandakan_4d_save($data)){
			return $td;
		}
		return false;
	}
	
	function _get_singapore_4d($td){
		$td['date'] = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $td['date']);		
		$td['date'] = date("Y-m-d",strtotime($td['date']));
		$td['draw'] = preg_replace("#.*([0-9]+).*#smiU", "$1", $td['draw']);
		$td['draw'] = $td['draw']."/".date("Y",strtotime($td['date']));
		
		$td['r_4d'] = explode("||",$td['r_4d']);
		$temp = array();
		foreach($td['r_4d'] as $value){
			if(strlen($value)==4 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_4d'] = $temp;
		
		$td['r_special'] = explode("||",$td['r_special']);
		$temp = array();
		foreach($td['r_special'] as $value){			
			preg_match_all("#.*([0-9]{4}).*#i", $value, $matches);
			if(ISSET($matches[1][0]) && strlen($matches[1][0])>0){
				$temp[] = $matches[1][0];
			}
		}
		$td['r_special'] = $temp;
		
		$td['r_consolation'] = explode("||",$td['r_consolation']);
		$temp = array();
		foreach($td['r_consolation'] as $value){			
			if(strlen($value)==4 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_consolation'] = $temp;
		
		$data = array(
			'silent' => true,
			'date' => $td['date'],
			'draw'=>$td['draw'],
			'r_1' => $td['r_4d'][0],
			'r_2' => $td['r_4d'][1],
			'r_3' => $td['r_4d'][2],
			'r_4' => $td['r_special'],
			'r_5' => $td['r_consolation']
		);
		if($this->CI->clotterymalaysia->singapore_4d_save($data)){
			return $td;
		}
		return false;
	}
	
	function _get_sabah_4d($td){
		$td['date'] = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $td['date']);		
		$td['date'] = date("Y-m-d",strtotime($td['date']));
		$td['draw'] = preg_replace("#.*([0-9]+/[0-9]+).*#smiU", "$1", $td['draw']);
		
		$td['r_4d'] = explode("||",$td['r_4d']);
		$temp = array();
		foreach($td['r_4d'] as $value){
			if(strlen($value)==4 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_4d'] = $temp;
		
		$td['r_special'] = explode("||",$td['r_special']);
		$temp = array();
		foreach($td['r_special'] as $value){			
			preg_match_all("#.*([0-9]{4}).*#i", $value, $matches);
			if(ISSET($matches[1][0]) && strlen($matches[1][0])>0){
				$temp[] = $matches[1][0];
			}
		}
		$td['r_special'] = $temp;
		
		$td['r_consolation'] = explode("||",$td['r_consolation']);
		$temp = array();
		foreach($td['r_consolation'] as $value){			
			if(strlen($value)==4 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_consolation'] = $temp;
		
		$data = array(
			'silent' => true,
			'date' => $td['date'],
			'draw'=>$td['draw'],
			'r_1' => $td['r_4d'][0],
			'r_2' => $td['r_4d'][1],
			'r_3' => $td['r_4d'][2],
			'r_4' => $td['r_special'],
			'r_5' => $td['r_consolation']
		);
		if($this->CI->clotterymalaysia->sabah_4d_save($data)){
			return $td;
		}
		return false;
	}
	
	function _get_sabah_3d($td){
		$td['date'] = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $td['date']);		
		$td['date'] = date("Y-m-d",strtotime($td['date']));
		$td['draw'] = preg_replace("#.*([0-9]+/[0-9]+).*#smiU", "$1", $td['draw']);
		
		$td['r_3d'] = explode("||",$td['r_3d']);
		$temp = array();
		foreach($td['r_3d'] as $value){
			if(strlen($value)==3 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_3d'] = $temp;		
		
		$data = array(
			'silent' => true,
			'date' => $td['date'],
			'draw'=>$td['draw'],
			'r_1' => $td['r_3d'][0],
			'r_2' => $td['r_3d'][1],
			'r_3' => $td['r_3d'][2]
		);
		if($this->CI->clotterymalaysia->sabah_3d_save($data)){
			return $td;
		}
		return false;
	}
	
	function _get_sabah_645($td){
		$td['date'] = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $td['date']);		
		$td['date'] = date("Y-m-d",strtotime($td['date']));
		$td['draw'] = preg_replace("#.*([0-9]+/[0-9]+).*#smiU", "$1", $td['draw']);
		
		$td['r_645'] = explode(",",$td['r_645']);
		$temp = array();
		foreach($td['r_645'] as $value){
			if(strlen($value)>=1 && strlen($value)<=2 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_645'] = $temp;
		
		$data = array(
			'silent' => true,
			'date' => $td['date'],
			'draw'=>$td['draw'],
			'r_645' => $td['r_645']
		);
		if($this->CI->clotterymalaysia->sabah_645_save($data)){
			return $td;
		}
		return false;
	}
	
	function _get_singapore_645($td){
		$td['date'] = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $td['date']);		
		$td['date'] = date("Y-m-d",strtotime($td['date']));
		$td['draw'] = preg_replace("#.*([0-9]+).*#smiU", "$1", $td['draw']);
		$td['draw'] = $td['draw']."/".date("Y",strtotime($td['date']));
		
		$td['r_645'] = explode(",",$td['r_645']);
		$temp = array();
		foreach($td['r_645'] as $value){
			if(strlen($value)>=1 && strlen($value)<=2 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_645'] = $temp;
		
		$data = array(
			'silent' => true,
			'date' => $td['date'],
			'draw'=>$td['draw'],
			'r_645' => $td['r_645']
		);
		if($this->CI->clotterymalaysia->singapore_645_save($data)){
			return $td;
		}
		return false;
	}
	
	function _get_damacai_6d($td){
		$td['date'] = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $td['date']);		
		$td['date'] = date("Y-m-d",strtotime($td['date']));
		$td['draw'] = preg_replace("#.*([0-9]+/[0-9]+).*#smiU", "$1", $td['draw']);
		
		$td['r_6d'] = explode("||",$td['r_6d']);
		$temp = array();
		foreach($td['r_6d'] as $value){
			$temp2 = explode(",",$value);
			if(strlen($temp2[0])==6 && is_numeric($temp2[0])!==FALSE && strlen($temp2[1])>=1 && strlen($temp2[1])<=2 && is_numeric($temp2[1])!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_6d'] = $temp;
		
		$td['r_special'] = explode("||",$td['r_special']);
		$temp = array();
		foreach($td['r_special'] as $value){			
			if(strlen($value)==6 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_special'] = $temp;
		
		$td['r_consolation'] = explode("||",$td['r_consolation']);
		$temp = array();
		foreach($td['r_consolation'] as $value){			
			if(strlen($value)==6 && is_numeric($value)!==FALSE){
				$temp[] = $value;
			}
		}
		$td['r_consolation'] = $temp;
		
		$data = array(
			'silent' => true,
			'date' => $td['date'],
			'draw'=>$td['draw'],
			'r_1' => $td['r_6d'][0],
			'r_2' => $td['r_6d'][1],
			'r_3' => $td['r_6d'][2],
			'r_4' => $td['r_special'],
			'r_5' => $td['r_consolation']
		);
		if($this->CI->clotterymalaysia->damacai_6d_save($data)){
			return $td;
		}
		return false;
	}
	
}

?>

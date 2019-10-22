<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 4D88 class
 */
class Clottery4d88{
	
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
		$this->CI->load->model('mlottery');
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
		$td['from'] = trim(urldecode($_GET['from']));
		$td['title'] = ((ISSET($_GET['title']))?trim(urldecode($_GET['title'])):"");
		$status = 0;
		$data = "";
		$method_name = "_get_".$td['from']."_".$td['title'];
		
		if(method_exists($this, $method_name)){
			$td = $this->$method_name($td);
			if(ISSET($td['date']) && STRLEN($td['date'])==10 && checkdate(substr($td['date'],5,2), substr($td['date'],8,2), substr($td['date'],0,4))){
				$rec = 0;
				$result = $this->CI->mlotteryresource->get_history($td['date'],$td['from'],'3d');
				if(!$result){	
					if(isset($td['r_3d']) && sizeof($td['r_3d'])==3){
						for($i=0; $i<sizeof($td['r_3d']); $i++){
							if($this->CI->mlotteryresource->result_insert('3d', $td['from'], $td['draw'], $td['date'], '3d', $td['r_3d'][$i], ($i+1))){
								$rec += 1;
								if($i==(sizeof($td['r_3d']))-1){
									$this->CI->mlotteryresource->history_insert($td['date'],$td['from'],'3d',$this->resource);							
								}
							}
						}				
					}
				}
				$result = $this->CI->mlotteryresource->get_history($td['date'],$td['from'],'4d');
				if(!$result){
					if(isset($td['r_4d']) && sizeof($td['r_4d'])==3){
						for($i=0; $i<sizeof($td['r_4d']); $i++){
							if($this->CI->mlotteryresource->result_insert('4d', $td['from'], $td['draw'], $td['date'], '4d', $td['r_4d'][$i], ($i+1))){
								$rec += 1;
								if($i==(sizeof($td['r_4d']))-1){
									$this->CI->mlotteryresource->history_insert($td['date'],$td['from'],'4d',$this->resource);							
								}
							}
						}				
					}
				}
				$result = $this->CI->mlotteryresource->get_history($td['date'],$td['from'],'special');
				if(!$result){
					if(isset($td['r_special']) && sizeof($td['r_special'])==10){
						for($i=0; $i<sizeof($td['r_special']); $i++){
							if($this->CI->mlotteryresource->result_insert('4d', $td['from'], $td['draw'], $td['date'], 'special', $td['r_special'][$i], '4')){
								$rec += 1;
								if($i==(sizeof($td['r_special']))-1){
									$this->CI->mlotteryresource->history_insert($td['date'],$td['from'],'special',$this->resource);							
								}						
							}
						}
					}
				}
				$result = $this->CI->mlotteryresource->get_history($td['date'],$td['from'],'consolation');
				if(!$result){
					if(isset($td['r_consolation']) && sizeof($td['r_consolation'])==10){
						for($i=0; $i<sizeof($td['r_consolation']); $i++){
							if($this->CI->mlotteryresource->result_insert('4d', $td['from'], $td['draw'], $td['date'], 'consolation', $td['r_consolation'][$i], '5')){
								$rec += 1;
								if($i==(sizeof($td['r_consolation']))-1){
									$this->CI->mlotteryresource->history_insert($td['date'],$td['from'],'consolation',$this->resource);							
								}						
							}
						}				
					}												
				}
				$result = $this->CI->mlotteryresource->get_history($td['date'],$td['from'],'5d');
				if(!$result){
					if(isset($td['r_5d']) && sizeof($td['r_5d'])==3){
						for($i=0; $i<sizeof($td['r_5d']); $i++){
							if($this->CI->mlotteryresource->result_insert('5d', $td['from'], $td['draw'], $td['date'], '5d', $td['r_5d'][$i], ($i+1))){
								$rec += 1;
								if($i==(sizeof($td['r_5d']))-1){
									$this->CI->mlotteryresource->history_insert($td['date'],$td['from'],'5d',$this->resource);							
								}						
							}
						}				
					}												
				}
				$result = $this->CI->mlotteryresource->get_history($td['date'],$td['from'],'6d');
				if(!$result){
					if(isset($td['r_6d']) && strlen($td['r_6d'])==6){
						if($this->CI->mlotteryresource->result_insert('6d', $td['from'], $td['draw'], $td['date'], '6d', $td['r_6d'], '1')){
							$rec += 1;
							$this->CI->mlotteryresource->history_insert($td['date'],$td['from'],'6d',$this->resource);
						}
					}												
				}
				$result = $this->CI->mlotteryresource->get_history($td['date'],$td['from'],'645');
				if(!$result){
					if(array_search($td['from'], array('sabah','singapore'))!==FALSE && isset($td['r_645']) && sizeof($td['r_645'])==7){
						if($this->CI->mlotteryresource->result_insert('jackpot', $td['from'], $td['draw'], $td['date'], '645', $td['r_645'], '1')){
							$rec += 1;
							$this->CI->mlotteryresource->history_insert($td['date'],$td['from'],'645',$this->resource);
						}
					}												
				}				
				$result = $this->CI->mlotteryresource->get_history($td['date'],$td['from'],'658');
				if(!$result){
					if(isset($td['r_658']) && sizeof($td['r_658'])==6){
						if($this->CI->mlotteryresource->result_insert('jackpot', $td['from'], $td['draw'], $td['date'], '658', $td['r_658'], '1')){
							$rec += 1;
							$this->CI->mlotteryresource->history_insert($td['date'],$td['from'],'658',$this->resource);
						}
					}												
				}
				$result = $this->CI->mlotteryresource->get_history($td['date'],$td['from'],'655');
				if(!$result){
					if(isset($td['r_655']) && sizeof($td['r_655'])==6){
						if($this->CI->mlotteryresource->result_insert('jackpot', $td['from'], $td['draw'], $td['date'], '655', $td['r_655'], '1')){
							$rec += 1;
							$this->CI->mlotteryresource->history_insert($td['date'],$td['from'],'655',$this->resource);
						}
					}												
				}
				$result = $this->CI->mlotteryresource->get_history($td['date'],$td['from'],'652');
				if(!$result){
					if(isset($td['r_652']) && sizeof($td['r_652'])>=6 && sizeof($td['r_652'])<=7){
						if($this->CI->mlotteryresource->result_insert('jackpot', $td['from'], $td['draw'], $td['date'], '652', $td['r_652'], '1')){
							$rec += 1;
							$this->CI->mlotteryresource->history_insert($td['date'],$td['from'],'652',$this->resource);
						}
					}												
				}
				if($rec>0){
					$status = 1;
					$data = $td['from']." --- ".$td['date']." --- ".$rec." record(s) --- updated on ".date("Y-m-d H:i:s");
				}	
			}	
		}		
		
		return json_encode(array('status'=>$status,'data'=>$data));
	}
	
	function _get_magnum_4d($td){
		$td['date'] = trim(urldecode($_GET['date']));
		$td['draw'] = trim(urldecode($_GET['draw']));		
		$td['r_4d'] = trim(urldecode($_GET['r_4d']));
		$td['r_special'] = trim(urldecode($_GET['r_special']));
		$td['r_consolation'] = trim(urldecode($_GET['r_consolation']));		
		
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
		
		return $td;
	}
	
	function _get_damacai_4d($td){
		$td['date'] = trim(urldecode($_GET['date']));
		$td['draw'] = trim(urldecode($_GET['draw']));
		$td['r_4d'] = trim(urldecode($_GET['r_4d']));
		$td['r_special'] = trim(urldecode($_GET['r_special']));
		$td['r_consolation'] = trim(urldecode($_GET['r_consolation']));		
		
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
		
		return $td;
	}
	
	function _get_toto_4d($td){
		$td['date'] = trim(urldecode($_GET['date']));
		$td['draw'] = trim(urldecode($_GET['draw']));		
		$td['r_4d'] = trim(urldecode($_GET['r_4d']));
		$td['r_special'] = trim(urldecode($_GET['r_special']));
		$td['r_consolation'] = trim(urldecode($_GET['r_consolation']));				
		
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
		
		return $td;
	}
	
	function _get_toto_5d($td){
		$td['date'] = trim(urldecode($_GET['date']));
		$td['draw'] = trim(urldecode($_GET['draw']));
		$td['r_5d'] = trim(urldecode($_GET['r_5d']));				
		
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
		
		return $td;
	}
	
	function _get_toto_6d($td){
		$td['date'] = trim(urldecode($_GET['date']));
		$td['draw'] = trim(urldecode($_GET['draw']));
		$td['r_6d'] = trim(urldecode($_GET['r_6d']));
		
		$td['date'] = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $td['date']);		
		$td['date'] = date("Y-m-d",strtotime($td['date']));
		$td['draw'] = preg_replace("#.*([0-9]+/[0-9]+).*#smiU", "$1", $td['draw']);
				
		if(strlen($td['r_6d'])<>6 || is_numeric($td['r_6d'])===FALSE){
			$td['r_6d'] = null;			
		}
		
		return $td;
	}
	
	function _get_toto_658($td){
		$td['date'] = trim(urldecode($_GET['date']));
		$td['draw'] = trim(urldecode($_GET['draw']));
		$td['r_658'] = trim(urldecode($_GET['r_658']));
		
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
		
		return $td;
	}
	
	function _get_toto_655($td){
		$td['date'] = trim(urldecode($_GET['date']));
		$td['draw'] = trim(urldecode($_GET['draw']));		
		$td['r_655'] = trim(urldecode($_GET['r_655']));		
		
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
		
		return $td;
	}
	
	function _get_toto_652($td){
		$td['date'] = trim(urldecode($_GET['date']));
		$td['draw'] = trim(urldecode($_GET['draw']));		
		$td['r_652'] = trim(urldecode($_GET['r_652']));		
		
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
		
		return $td;
	}
	
	function _get_sweep_4d($td){
		$td['date'] = trim(urldecode($_GET['date']));
		$td['draw'] = trim(urldecode($_GET['draw']));
		$td['r_4d'] = trim(urldecode($_GET['r_4d']));
		$td['r_special'] = trim(urldecode($_GET['r_special']));
		$td['r_consolation'] = trim(urldecode($_GET['r_consolation']));		
		
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
		
		return $td;
	}
	
	function _get_sandakan_4d($td){
		$td['date'] = trim(urldecode($_GET['date']));
		$td['draw'] = trim(urldecode($_GET['draw']));		
		$td['r_4d'] = trim(urldecode($_GET['r_4d']));
		$td['r_special'] = trim(urldecode($_GET['r_special']));
		$td['r_consolation'] = trim(urldecode($_GET['r_consolation']));		
		
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
		
		return $td;
	}
	
	function _get_singapore_4d($td){
		$td['date'] = trim(urldecode($_GET['date']));
		$td['draw'] = trim(urldecode($_GET['draw']));		
		$td['r_4d'] = trim(urldecode($_GET['r_4d']));
		$td['r_special'] = trim(urldecode($_GET['r_special']));
		$td['r_consolation'] = trim(urldecode($_GET['r_consolation']));		
		
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
		
		return $td;
	}
	
	function _get_sabah_4d($td){
		$td['date'] = trim(urldecode($_GET['date']));
		$td['draw'] = trim(urldecode($_GET['draw']));		
		$td['r_4d'] = trim(urldecode($_GET['r_4d']));
		$td['r_special'] = trim(urldecode($_GET['r_special']));
		$td['r_consolation'] = trim(urldecode($_GET['r_consolation']));		
		
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
		
		return $td;
	}
	
	function _get_sabah_3d($td){
		$td['date'] = trim(urldecode($_GET['date']));
		$td['draw'] = trim(urldecode($_GET['draw']));		
		$td['r_3d'] = trim(urldecode($_GET['r_3d']));		
		
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
		
		return $td;
	}
	
	function _get_sabah_645($td){
		$td['date'] = trim(urldecode($_GET['date']));
		$td['draw'] = trim(urldecode($_GET['draw']));		
		$td['r_645'] = trim(urldecode($_GET['r_645']));		
		
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
		
		return $td;
	}
	
	function _get_singapore_645($td){
		$td['date'] = trim(urldecode($_GET['date']));
		$td['draw'] = trim(urldecode($_GET['draw']));		
		$td['r_645'] = trim(urldecode($_GET['r_645']));		
		
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
		
		return $td;
	}
	
}

?>

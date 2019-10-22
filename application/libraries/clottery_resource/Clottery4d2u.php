<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 4d2u.com class
 */
class Clottery4d2u{
	
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
	var $resource = "4D2U.COM";
	
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
		$data = file_get_contents('http://www.4d2u.com/allresult.php?s=&lang=E&drawdate='.$batch_no);
		$data = iconv('GB2312','UTF-8', $data);			
		preg_match_all('#<body[^>]*>(.*)</body>#smiU', $data, $matches);
		$data = $matches[1][0];
		preg_match_all('#<script.*</script>#smiU', $data, $matches);
		if($matches){				
			for($i=0; $i<sizeof($matches); $i++){
				$data = str_replace($matches[$i], '', $data);
			}
		}		
		ob_end_clean();
		
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
			$data = file_get_contents('http://4dresult.info/?lang=e');
			preg_match_all('#<body[^>]*>(.*)</body>#smiU', $data, $matches);
			$data = $matches[1][0];
			preg_match_all('#<script.*</script>#smiU', $data, $matches);
			if($matches){				
				for($i=0; $i<sizeof($matches); $i++){
					$data = str_replace($matches[$i], '', $data);
				}
			}				
			$data = iconv('GB2312','UTF-8', $data);			
			ob_end_clean();			
		}
		
		if(strlen($data)>0){
			$status = 1;
		}
		
		return json_encode(array('status'=>$status,'data'=> $data));
	}
	
	function _check_type($data){
		$result = array(
			'step' => 1,
			'from' => "",
			'oadd' => 0,
			'table' => "",
			'method' => 1,
			'title' => ""
		);
		$title = ";".str_ireplace("\n", ";", $data->title).";";		
		
		//from
		$from = str_ireplace("\n","",trim($data->from));
		if($from=="Sabah"){
			$result['from'] = "sabah";			
		}else if($from=="Magnum"){
			$result['from'] = "magnum";			
		}else if($from=="Da Ma Cai 1 3D (PMP 1 3D)"){
			$result['from'] = "damacai";			
		}else if($from=="Sandakan"){
			$result['from'] = "sandakan";			
		}else if($from=="Singapore"){
			if(array_search($title, array(";3d first;",";first;",";special;",";consolation;"))){
				$result['from'] = "singapore";
			}		
		}else if($from=="Singapore 6/45"){
			$result['from'] = "singapore";			
		}else if($from=="Special Cash Sweep 1 3D"){
			$result['from'] = "sweep";			
		}else if($from=="Toto 4D"){
			$result['from'] = "toto";			
		}else{
			return false;
		}		
		
		//title		
		if(stripos($title, ";3d first;")!==false){
			$result['step'] = 1;
			$result['oadd'] = 1;
			$result['table'] = "3d";	
			$result['title'] = "3d";
		}else if(stripos($title, ";first;")!==false){
			$result['step'] = 1;
			$result['oadd'] = 1;
			$result['table'] = "4d";			
			$result['title'] = "4d";
		}else if(stripos($title, ";special;")!==false){
			$result['step'] = 4;			
			$result['table'] = "4d";
			$result['title'] = "special";
		}else if(stripos($title, ";consolation;")!==false){
			$result['step'] = 5;
			$result['table'] = "4d";
			$result['title'] = "consolation";
		}else if(stripos($title, ";Toto 5D;")!==false){			
			$result['step'] = 1;
			$result['oadd'] = 1;
			$result['table'] = "5d";
			$result['title'] = "5d";
		}else if(stripos($title, ";Toto 6D;")!==false){			
			$result['step'] = 1;
			$result['oadd'] = 1;
			$result['table'] = "6d";
			$result['title'] = "6d";
		}else if($result['from']=="toto" && stripos($title, ";Mega Toto 6/52;")!==false){			
			$result['step'] = 1;
			$result['oadd'] = 1;
			$result['method'] = 2;
			$result['table'] = "jackpot";
			$result['title'] = "652";
		}else if($result['from']=="toto" && stripos($title, ";Power Toto 6/55;")!==false){			
			$result['step'] = 1;
			$result['oadd'] = 1;
			$result['method'] = 2;
			$result['table'] = "jackpot";
			$result['title'] = "655";
		}else if($result['from']=="toto" && stripos($title, ";Supreme Toto 6/58;")!==false){			
			$result['step'] = 1;
			$result['oadd'] = 1;
			$result['method'] = 2;
			$result['table'] = "jackpot";
			$result['title'] = "658";
		}else if($result['from']=="sabah" && stripos($title, ";Sabah Lotto 6/45;")!==false){			
			$result['step'] = 1;
			$result['oadd'] = 1;
			$result['method'] = 2;
			$result['table'] = "jackpot";
			$result['title'] = "645";
		}else if($result['from']=="singapore" && stripos($title, ";Singapore Toto 6/45;")!==false){			
			$result['step'] = 1;
			$result['oadd'] = 1;
			$result['method'] = 2;
			$result['table'] = "jackpot";
			$result['title'] = "645";
		}else{
			return false;
		}
		
		return $result;
	}
	
	function save_endata(){
		$tdata = new stdClass;		
		$tdata->from = urldecode($_GET['from']);
		$tdata->date = urldecode($_GET['date']);
		$tdata->drawid = urldecode($_GET['drawid']);
		$tdata->title = urldecode($_GET['title']);
		$tdata->text = urldecode($_GET['data']);		
		$status = 0;
		$data = "";
		if(strlen(trim($tdata->date))>0){
			$tdata->date = preg_replace("#([0-9]{2})/([0-9]{2})/([0-9]{4}).*#i", "$3-$2-$1", $tdata->date);
		}		
		/*
		 * process
		 */		
		$status = intval($this->_calculation($tdata));
		if($status>0){
			$data = $tdata->from." --- ".$tdata->date." --- updated on ".date("Y-m-d H:i:s");
		}
		return json_encode(array('status'=>$status,'data'=> $data));
	}
	
	function _calculation($data){		
		$error = 0;		
		$temp = explode("\n",$data->text);
		if(sizeof($temp)>0){
			$type = $this->_check_type($data);	
			if($type){
				$result = $this->CI->mlotteryresource->get_history($data->date,$type['from'],$type['title']);
				if($result){
				}else{
					if(isset($type['from']) && strlen($type['from'])>0 && isset($type['table']) && strlen($type['table'])>0){
						$step = $type['step'];				
						if($type['method']==1){
							foreach($temp as $value){
								if(strlen(trim($value))>0 && is_numeric(trim($value))){
									$value = trim($value);
									if(!$this->CI->mlotteryresource->result_insert($type['table'], $type['from'], $data->drawid, $data->date, $type['title'], $value, $step)){
										$error += 1;
									}
									if($type['oadd']){
										$step += 1;
									}					
								}
							}
						}else if($type['method']==2){
							if(!$this->CI->mlotteryresource->result_insert($type['table'], $type['from'], $data->drawid, $data->date, $type['title'], $temp, $step)){
								$error += 1;
							}
						}else{
							$error += 1;
						}
						if($error == 0){						
							$this->CI->mlotteryresource->history_insert($data->date,$type['from'],$type['title'],$this->resource);
							return true;
						}
					}
				}			
			}
		}
		return false;
	}
}

?>

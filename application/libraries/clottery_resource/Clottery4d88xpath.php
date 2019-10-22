<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/*
 * 4D88 class with xpath
 */

class Clottery4d88xpath {

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
		$this->CI = & get_instance();
		$this->CI->load->library('clottery_class/clotterymalaysia');
		$this->CI->load->library('ccfg');
		$this->CI->clotteryresource->resource = $this->resource;
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

	function endata() {
		$cdate = date("Y-m-d H:i:s");
		$fdate = date(date("Y-m-d", strtotime($cdate)) . " " . $this->start_time);
		$tdate = date(date("Y-m-d", strtotime($cdate)) . " " . $this->end_time);
		$wy = date("l", strtotime($cdate));
		$batch_no = date("Y-m-d", strtotime($cdate));
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
			if (strlen($data)) {
				preg_match_all('#<script.*</script>#smiU', $data, $matches);
				if ($matches) {
					for ($i = 0; $i < sizeof($matches); $i++) {
						$data = str_replace($matches[$i], '', $data);
					}
				}
			}
		}

		if (strlen($data) > 0) {
			return $this->save_endata($data);
		}
	}

	function save_endata($html) {
		$this->_get_magnum_4d($html);		
		$this->_get_damacai_4d($html);
		$this->_get_damacai_6d($html);
		$this->_get_toto_4d($html);
		$this->_get_toto_jackpot($html);
		$this->_get_sabah_4d($html);
		$this->_get_sandakan_4d($html);
		$this->_get_sweep_4d($html);
		$this->_get_sabah_jackpot($html);
		$this->_get_singapore_4d($html);
		$this->_get_singapore_jackpot($html);
	}

	function _get_magnum_4d($html) {
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		$xpath = new DOMXPath($doc);

		$element = $xpath->query('//font[text()="MAGNUM 4D"]');
		if(!$element || $element->length==0){
			return false;
		}
		$title = $element->item(0)->nodeValue;
		$date = $xpath->query('./ancestor::strong[1]/font[2]', $element->item(0))->item(0)->nodeValue;
		$date = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $date);
		$date = date("Y-m-d", strtotime($date));
		$drawid = $xpath->query('./ancestor::strong[1]/font[3]', $element->item(0))->item(0)->nodeValue;
		$drawid = preg_replace("#[^\d/]#smiU", "", $drawid);
		$temp = $xpath->query('./ancestor::table[2]/tr[2]//div', $element->item(0));
		$temp2 = array();
		foreach ($temp as $e) {
			if (trim($e->nodeValue)>0) {
				$temp2[] = trim($e->nodeValue);
			}
		}
		$r_4d = $temp2;

		$temp = $xpath->query('./ancestor::table[2]/tr[3]//table//table', $element->item(0));
		$temp2 = $xpath->query('.//td[@class="linesbox"]', $temp->item(0));
		$temp3 = array();
		foreach ($temp2 as $e) {
			if (trim($e->nodeValue)>0 && is_numeric(trim($e->nodeValue))) {
				$temp3[] = trim($e->nodeValue);
			}
		}
		$r_special = $temp3;

		$temp2 = $xpath->query('.//td[@class="linesbox"]', $temp->item(1));
		$temp3 = array();
		foreach ($temp2 as $e) {
			if (trim($e->nodeValue)>0 && is_numeric(trim($e->nodeValue))) {
				$temp3[] = trim($e->nodeValue);
			}
		}
		$r_consolation = $temp3;

		$data = array(
			'date' => $date,
			'draw'=>$drawid,
			'r_1' => $r_4d[0],
			'r_2' => $r_4d[1],
			'r_3' => $r_4d[2],
			'r_4' => $r_special,
			'r_5' => $r_consolation
		);
		return $this->CI->clotterymalaysia->magnum_4d_save($data);
	}

	function _get_damacai_4d($html) {
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		$xpath = new DOMXPath($doc);

		$element = $xpath->query('//font[text()="DAMACAI 1+3D"]');
		if(!$element || $element->length==0){
			return false;
		}
		$title = $element->item(0)->nodeValue;
		$date = $xpath->query('./ancestor::strong[1]/font[2]', $element->item(0))->item(0)->nodeValue;
		$date = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $date);
		$date = date("Y-m-d", strtotime($date));
		$drawid = $xpath->query('./ancestor::strong[1]/font[3]', $element->item(0))->item(0)->nodeValue;
		$drawid = preg_replace("#[^\d/]#smiU", "", $drawid);
		$temp = $xpath->query('./ancestor::table[2]/tr[2]//div', $element->item(0));
		$temp2 = array();
		foreach ($temp as $e) {
			if (trim($e->nodeValue)>0) {
				$temp2[] = trim($e->nodeValue);
			}
		}
		$r_4d = $temp2;

		$temp = $xpath->query('./ancestor::table[2]/tr[3]//table//table', $element->item(0));
		$temp2 = $xpath->query('.//td[@class="linesbox"]', $temp->item(0));
		$temp3 = array();
		foreach ($temp2 as $e) {
			if (trim($e->nodeValue)>0 && is_numeric(trim($e->nodeValue))) {
				$temp3[] = trim($e->nodeValue);
			}
		}
		$r_special = $temp3;

		$temp2 = $xpath->query('.//td[@class="linesbox"]', $temp->item(1));
		$temp3 = array();
		foreach ($temp2 as $e) {
			if (trim($e->nodeValue)>0 && is_numeric(trim($e->nodeValue))) {
				$temp3[] = trim($e->nodeValue);
			}
		}
		$r_consolation = $temp3;

		$data = array(
			'date' => $date,
			'draw'=>$drawid,
			'r_1' => $r_4d[0],
			'r_2' => $r_4d[1],
			'r_3' => $r_4d[2],
			'r_4' => $r_special,
			'r_5' => $r_consolation
		);
		return $this->CI->clotterymalaysia->damacai_4d_save($data);
	}
	
	function _get_damacai_6d($html) {
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		$xpath = new DOMXPath($doc);

		$element = $xpath->query('//font[text()="DAMACAI 6D"]');
		if(!$element || $element->length==0){
			return false;
		}
		$title = $element->item(0)->nodeValue;
		$date = $xpath->query('./ancestor::strong[1]/font[3]', $element->item(0))->item(0)->nodeValue;
		$date = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $date);
		$date = date("Y-m-d", strtotime($date));
		$drawid = $xpath->query('./ancestor::strong[1]/font[4]', $element->item(0))->item(0)->nodeValue;
		$drawid = preg_replace("#[^\d/]#smiU", "", $drawid);
		$temp = $xpath->query('./ancestor::table[2]/tr[2]//div', $element->item(0));
		$temp2 = array();
		foreach ($temp as $e) {
			if (trim($e->nodeValue)>0) {
				$temp_str = trim($e->nodeValue);
				$temp_str = preg_replace('#[^\d\+]#Ui', "", $temp_str);
				$temp_str = preg_replace('#[\+]#Ui', ",", $temp_str);
				$temp2[] = $temp_str;
			}
		}
		$r_4d = $temp2;

		$temp = $xpath->query('./ancestor::table[2]/tr[3]//table//table', $element->item(0));
		$temp2 = $xpath->query('.//td[@class="slinesbox"]', $temp->item(0));
		$temp3 = array();
		foreach ($temp2 as $e) {
			if (trim($e->nodeValue)>0 && is_numeric(trim($e->nodeValue))) {
				$temp3[] = trim($e->nodeValue);
			}
		}
		$r_special = $temp3;

		$temp2 = $xpath->query('.//td[@class="slinesbox"]', $temp->item(1));
		$temp3 = array();
		foreach ($temp2 as $e) {
			if (trim($e->nodeValue)>0 && is_numeric(trim($e->nodeValue))) {
				$temp3[] = trim($e->nodeValue);
			}
		}
		$r_consolation = $temp3;

		$data = array(
			'date' => $date,
			'draw'=>$drawid,
			'r_1' => $r_4d[0],
			'r_2' => $r_4d[1],
			'r_3' => $r_4d[2],
			'r_4' => $r_special,
			'r_5' => $r_consolation
		);
		return $this->CI->clotterymalaysia->damacai_6d_save($data);
	}

	function _get_toto_4d($html) {
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		$xpath = new DOMXPath($doc);

		$element = $xpath->query('//font[text()="TOTO 4D"]');
		if(!$element || $element->length==0){
			return false;
		}
		$title = $element->item(0)->nodeValue;
		$date = $xpath->query('./ancestor::strong[1]/font[2]', $element->item(0))->item(0)->nodeValue;
		$date = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $date);
		$date = date("Y-m-d", strtotime($date));
		$drawid = $xpath->query('./ancestor::strong[1]/font[3]', $element->item(0))->item(0)->nodeValue;
		$drawid = preg_replace("#[^\d/]#smiU", "", $drawid);
		$temp = $xpath->query('./ancestor::table[2]/tr[2]//div', $element->item(0));
		$temp2 = array();
		foreach ($temp as $e) {
			if (trim($e->nodeValue)>0) {
				$temp2[] = trim($e->nodeValue);
			}
		}
		$r_4d = $temp2;

		$temp = $xpath->query('./ancestor::table[2]/tr[3]//table//table', $element->item(0));
		$temp2 = $xpath->query('.//td[@class="linesbox"]', $temp->item(0));
		$temp3 = array();
		foreach ($temp2 as $e) {
			if (trim($e->nodeValue)>0 && is_numeric(trim($e->nodeValue))) {
				$temp3[] = trim($e->nodeValue);
			}
		}
		$r_special = $temp3;

		$temp2 = $xpath->query('.//td[@class="linesbox"]', $temp->item(1));
		$temp3 = array();
		foreach ($temp2 as $e) {
			if (trim($e->nodeValue)>0 && is_numeric(trim($e->nodeValue))) {
				$temp3[] = trim($e->nodeValue);
			}
		}
		$r_consolation = $temp3;

		$data = array(
			'date' => $date,
			'draw'=>$drawid,
			'r_1' => $r_4d[0],
			'r_2' => $r_4d[1],
			'r_3' => $r_4d[2],
			'r_4' => $r_special,
			'r_5' => $r_consolation
		);
		return $this->CI->clotterymalaysia->toto_4d_save($data);
	}

	function _get_toto_jackpot($html) {
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		$xpath = new DOMXPath($doc);		

		//mega toto
		$element = $xpath->query('//font[text()="MEGA TOTO"]');
		if(!$element || $element->length==0){
			return false;
		}
		$title = $element->item(0)->nodeValue;
		$date = $xpath->query('./ancestor::strong[1]/font[2]', $element->item(0))->item(0)->nodeValue;
		$date = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $date);
		$date = date("Y-m-d", strtotime($date));
		$drawid = $xpath->query('./ancestor::strong[1]/font[3]', $element->item(0))->item(0)->nodeValue;
		$drawid = preg_replace("#[^\d/]#smiU", "", $drawid);
		$temp = $xpath->query('./ancestor::table[2]//tr[@class="linesbox"]//td', $element->item(0));
		$temp2 = array();
		foreach ($temp as $e) {
			if (trim($e->nodeValue)>0) {
				$temp2[] = trim(preg_replace('#[^\d]*#iU', '', $e->nodeValue));
			}
		}
		$temp = intval($xpath->query('./ancestor::table[2]//font[@color="white"]', $element->item(0))->item(0)->nodeValue);
		if($temp>0){
			$temp2[] = $temp;
		}
		$r_652 = implode(",", $temp2);		

		//power toto
		$element = $xpath->query('//font[text()="POWER TOTO"]');
		if(!$element || $element->length==0){
			return false;
		}
		$title = $element->item(0)->nodeValue;
		$date = $xpath->query('./ancestor::strong[1]/font[2]', $element->item(0))->item(0)->nodeValue;
		$date = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $date);
		$date = date("Y-m-d", strtotime($date));
		$drawid = $xpath->query('./ancestor::strong[1]/font[3]', $element->item(0))->item(0)->nodeValue;
		$drawid = preg_replace("#[^\d/]#smiU", "", $drawid);
		$temp = $xpath->query('./ancestor::table[2]//tr[@class="linesbox"]//td', $element->item(0));
		$temp2 = array();
		foreach ($temp as $e) {
			if (trim($e->nodeValue)>0) {
				$temp2[] = trim(preg_replace('#[^\d]*#iU', '', $e->nodeValue));
			}
		}
		$r_655 = implode(",", $temp2);

		//supreme toto
		$element = $xpath->query('//font[text()="SUPREME TOTO"]');
		if(!$element || $element->length==0){
			return false;
		}
		$title = $element->item(0)->nodeValue;
		$date = $xpath->query('./ancestor::strong[1]/font[2]', $element->item(0))->item(0)->nodeValue;
		$date = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $date);
		$date = date("Y-m-d", strtotime($date));
		$drawid = $xpath->query('./ancestor::strong[1]/font[3]', $element->item(0))->item(0)->nodeValue;
		$drawid = preg_replace("#[^\d/]#smiU", "", $drawid);
		$temp = $xpath->query('./ancestor::table[2]//tr[@class="linesbox"]//td', $element->item(0));
		$temp2 = array();
		foreach ($temp as $e) {
			if (trim($e->nodeValue)>0) {
				$temp2[] = trim(preg_replace('#[^\d]*#iU', '', $e->nodeValue));
			}
		}
		$r_658 = implode(",", $temp2);

		//toto 5d
		$element = $xpath->query('//font[text()="TOTO 5D"]');
		if(!$element || $element->length==0){
			return false;
		}
		$title = $element->item(0)->nodeValue;
		$date = $xpath->query('./ancestor::strong[1]/font[2]', $element->item(0))->item(0)->nodeValue;
		$date = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $date);
		$date = date("Y-m-d", strtotime($date));
		$drawid = $xpath->query('./ancestor::strong[1]/font[3]', $element->item(0))->item(0)->nodeValue;
		$drawid = preg_replace("#[^\d/]#smiU", "", $drawid);
		$temp = $xpath->query('./ancestor::table[2]/tr[2]//td[@class="linesbox"]', $element->item(0));
		$temp2 = array();
		foreach ($temp as $e) {
			if (trim($e->nodeValue)>0 && strlen(trim($e->nodeValue))==5) {
				$temp2[] = trim($e->nodeValue);
			}
		}
		$r_5d = $temp2;

		//toto 6d
		$element = $xpath->query('//font[text()="TOTO 6D"]');
		if(!$element || $element->length==0){
			return false;
		}
		$title = $element->item(0)->nodeValue;
		$date = $xpath->query('./ancestor::strong[1]/font[2]', $element->item(0))->item(0)->nodeValue;
		$date = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $date);
		$date = date("Y-m-d", strtotime($date));
		$drawid = $xpath->query('./ancestor::strong[1]/font[3]', $element->item(0))->item(0)->nodeValue;
		$drawid = preg_replace("#[^\d/]#smiU", "", $drawid);
		$r_6d = $xpath->query('./ancestor::table[2]//td[@class="d3rdtxt"]', $element->item(0))->item(0)->nodeValue;
		
		$this->CI->load->library('clotteryresource');
		$temp = $this->CI->mlotteryresource->get_lottery_result_by_param("jackpot",array("cfrom"=>"toto","ctitle"=>"652","order_cdate"=>"desc","limit"=>"1"));
		if($temp && $temp[0]['cno']==$r_652){
			return false;
		}
		$temp = $this->CI->mlotteryresource->get_lottery_result_by_param("jackpot",array("cfrom"=>"toto","ctitle"=>"655","order_cdate"=>"desc","limit"=>"1"));
		if($temp && $temp[0]['cno']==$r_655){
			return false;
		}
		$temp = $this->CI->mlotteryresource->get_lottery_result_by_param("jackpot",array("cfrom"=>"toto","ctitle"=>"658","order_cdate"=>"desc","limit"=>"1"));
		if($temp && $temp[0]['cno']==$r_658){
			return false;
		}

		$data = array(
			'date' => $date,
			'draw'=>$drawid,
			'r_5d' => $r_5d,
			'r_6d' => $r_6d,
			'r_652' => $r_652,
			'r_655' => $r_655,
			'r_658' => $r_658
		);		
		return $this->CI->clotterymalaysia->toto_all_jackpot_save($data);
	}

	function _get_sabah_4d($html) {
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		$xpath = new DOMXPath($doc);

		//SABAH 3D
		$element = $xpath->query('//font[text()="SABAH 3D"]');
		if(!$element || $element->length==0){
			return false;
		}
		$title = $element->item(0)->nodeValue;
		$date = $xpath->query('./ancestor::strong[1]/font[2]', $element->item(0))->item(0)->nodeValue;
		$date = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $date);
		$date = date("Y-m-d", strtotime($date));
		$drawid = $xpath->query('./ancestor::strong[1]/font[3]', $element->item(0))->item(0)->nodeValue;
		$drawid = preg_replace("#[^\d/]#smiU", "", $drawid);
		$temp = $xpath->query('./ancestor::table[2]/tr[2]//div', $element->item(0));
		$temp2 = array();
		foreach ($temp as $e) {
			if (trim($e->nodeValue)>0) {
				$temp2[] = trim($e->nodeValue);
			}
		}
		$r_3d = $temp2;

		$element = $xpath->query('//font[text()="SABAH 4D"]');
		if(!$element || $element->length==0){
			return false;
		}
		$title = $element->item(0)->nodeValue;
		$date = $xpath->query('./ancestor::strong[1]/font[2]', $element->item(0))->item(0)->nodeValue;
		$date = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $date);
		$date = date("Y-m-d", strtotime($date));
		$drawid = $xpath->query('./ancestor::strong[1]/font[3]', $element->item(0))->item(0)->nodeValue;
		$drawid = preg_replace("#[^\d/]#smiU", "", $drawid);
		$temp = $xpath->query('./ancestor::table[2]/tr[2]//div', $element->item(0));
		$temp2 = array();
		foreach ($temp as $e) {
			if (trim($e->nodeValue)>0) {
				$temp2[] = trim($e->nodeValue);
			}
		}
		$r_4d = $temp2;

		$temp = $xpath->query('./ancestor::table[2]/tr[3]//table//table', $element->item(0));
		$temp2 = $xpath->query('.//td[@class="linesbox"]', $temp->item(0));
		$temp3 = array();
		foreach ($temp2 as $e) {
			if (trim($e->nodeValue)>0 && is_numeric(trim($e->nodeValue))) {
				$temp3[] = trim($e->nodeValue);
			}
		}
		$r_special = $temp3;

		$temp2 = $xpath->query('.//td[@class="linesbox"]', $temp->item(1));
		$temp3 = array();
		foreach ($temp2 as $e) {
			if (trim($e->nodeValue)>0 && is_numeric(trim($e->nodeValue))) {
				$temp3[] = trim($e->nodeValue);
			}
		}
		$r_consolation = $temp3;

		$data = array(
			'date' => $date,
			'draw'=>$drawid,
			'r_a1' => $r_3d[0],
			'r_a2' => $r_3d[1],
			'r_a3' => $r_3d[2],
			'r_1' => $r_4d[0],
			'r_2' => $r_4d[1],
			'r_3' => $r_4d[2],
			'r_4' => $r_special,
			'r_5' => $r_consolation
		);
		return $this->CI->clotterymalaysia->sabah_4d_save($data);
	}

	function _get_sandakan_4d($html) {
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		$xpath = new DOMXPath($doc);

		$element = $xpath->query('//font[text()="SANDAKAN 4D"]');
		if(!$element || $element->length==0){
			return false;
		}
		$title = $element->item(0)->nodeValue;
		$date = $xpath->query('./ancestor::strong[1]/font[2]', $element->item(0))->item(0)->nodeValue;
		$date = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $date);
		$date = date("Y-m-d", strtotime($date));
		$drawid = $xpath->query('./ancestor::strong[1]/font[3]', $element->item(0))->item(0)->nodeValue;
		$drawid = preg_replace("#[^\d/]#smiU", "", $drawid);
		$temp = $xpath->query('./ancestor::table[2]/tr[2]//div', $element->item(0));
		$temp2 = array();
		foreach ($temp as $e) {
			if (trim($e->nodeValue)>0) {
				$temp2[] = trim($e->nodeValue);
			}
		}
		$r_4d = $temp2;

		$temp = $xpath->query('./ancestor::table[2]/tr[3]//table//table', $element->item(0));
		$temp2 = $xpath->query('.//td[@class="linesbox"]', $temp->item(0));
		$temp3 = array();
		foreach ($temp2 as $e) {
			if (trim($e->nodeValue)>0 && is_numeric(trim($e->nodeValue))) {
				$temp3[] = trim($e->nodeValue);
			}
		}
		$r_special = $temp3;

		$temp2 = $xpath->query('.//td[@class="linesbox"]', $temp->item(1));
		$temp3 = array();
		foreach ($temp2 as $e) {
			if (trim($e->nodeValue)>0 && is_numeric(trim($e->nodeValue))) {
				$temp3[] = trim($e->nodeValue);
			}
		}
		$r_consolation = $temp3;

		$data = array(
			'date' => $date,
			'draw'=>$drawid,
			'r_1' => $r_4d[0],
			'r_2' => $r_4d[1],
			'r_3' => $r_4d[2],
			'r_4' => $r_special,
			'r_5' => $r_consolation
		);
		return $this->CI->clotterymalaysia->sandakan_4d_save($data);
	}

	function _get_sweep_4d($html) {
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		$xpath = new DOMXPath($doc);

		$element = $xpath->query('//font[text()="CASHSWEEP 1+3D"]');
		if(!$element || $element->length==0){
			return false;
		}
		$title = $element->item(0)->nodeValue;
		$date = $xpath->query('./ancestor::strong[1]/font[2]', $element->item(0))->item(0)->nodeValue;
		$date = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $date);
		$date = date("Y-m-d", strtotime($date));
		$drawid = $xpath->query('./ancestor::strong[1]/font[3]', $element->item(0))->item(0)->nodeValue;
		$drawid = preg_replace("#[^\d/]#smiU", "", $drawid);
		$temp = $xpath->query('./ancestor::table[2]/tr[2]//div', $element->item(0));
		$temp2 = array();
		foreach ($temp as $e) {
			if (trim($e->nodeValue)>0) {
				$temp2[] = trim($e->nodeValue);
			}
		}
		$r_4d = $temp2;

		$temp = $xpath->query('./ancestor::table[2]/tr[3]//table//table', $element->item(0));
		$temp2 = $xpath->query('.//td[@class="linesbox"]', $temp->item(0));
		$temp3 = array();
		foreach ($temp2 as $e) {
			if (trim($e->nodeValue)>0 && is_numeric(trim($e->nodeValue))) {
				$temp3[] = trim($e->nodeValue);
			}
		}
		$r_special = $temp3;

		$temp2 = $xpath->query('.//td[@class="linesbox"]', $temp->item(1));
		$temp3 = array();
		foreach ($temp2 as $e) {
			if (trim($e->nodeValue)>0 && is_numeric(trim($e->nodeValue))) {
				$temp3[] = trim($e->nodeValue);
			}
		}
		$r_consolation = $temp3;

		$data = array(
			'date' => $date,
			'draw'=>$drawid,
			'r_1' => $r_4d[0],
			'r_2' => $r_4d[1],
			'r_3' => $r_4d[2],
			'r_4' => $r_special,
			'r_5' => $r_consolation
		);
		return $this->CI->clotterymalaysia->sweep_4d_save($data);
	}

	function _get_sabah_jackpot($html) {
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		$xpath = new DOMXPath($doc);

		//SABAH LOTTO
		$element = $xpath->query('//font[text()="SABAH LOTTO"]');
		if(!$element || $element->length==0){
			return false;
		}
		$title = $element->item(0)->nodeValue;
		$date = $xpath->query('./ancestor::strong[1]/font[2]', $element->item(0))->item(0)->nodeValue;
		$date = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $date);
		$date = date("Y-m-d", strtotime($date));
		$drawid = $xpath->query('./ancestor::strong[1]/font[3]', $element->item(0))->item(0)->nodeValue;
		$drawid = preg_replace("#[^\d/]#smiU", "", $drawid);
		$temp = $xpath->query('./ancestor::table[2]//tr[@class="linesbox"]//td', $element->item(0));
		$temp2 = array();
		foreach ($temp as $e) {
			if (trim($e->nodeValue)>0) {
				$temp2[] = trim(preg_replace('#[^\d]*#iU', '', $e->nodeValue));
			}
		}
		$r_645 = implode(",", $temp2);

		$data = array(
			'date' => $date,
			'draw'=>$drawid,
			'r_645' => $r_645
		);
		return $this->CI->clotterymalaysia->sabah_645_save($data);
	}

	function _get_singapore_4d($html) {
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		$xpath = new DOMXPath($doc);

		$element = $xpath->query('//font[text()="SINGAPORE 4D"]');
		if(!$element || $element->length==0){
			return false;
		}
		$title = $element->item(0)->nodeValue;
		$date = $xpath->query('./ancestor::strong[1]/font[2]', $element->item(0))->item(0)->nodeValue;
		$date = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $date);
		$date = date("Y-m-d", strtotime($date));
		$drawid = $xpath->query('./ancestor::strong[1]/font[3]', $element->item(0))->item(0)->nodeValue;
		$drawid = preg_replace("#[^\d/]#smiU", "", $drawid);
		$temp = $xpath->query('./ancestor::table[2]/tr[2]//div', $element->item(0));
		$temp2 = array();
		foreach ($temp as $e) {
			if (trim($e->nodeValue)>0) {
				$temp2[] = trim($e->nodeValue);
			}
		}
		$r_4d = $temp2;

		$temp = $xpath->query('./ancestor::table[2]/tr[3]//table//table', $element->item(0));
		$temp2 = $xpath->query('.//td[@class="linesbox"]', $temp->item(0));
		$temp3 = array();
		foreach ($temp2 as $e) {
			if (trim($e->nodeValue)>0 && is_numeric(trim($e->nodeValue))) {
				$temp3[] = trim($e->nodeValue);
			}
		}
		$r_special = $temp3;

		$temp2 = $xpath->query('.//td[@class="linesbox"]', $temp->item(1));
		$temp3 = array();
		foreach ($temp2 as $e) {
			if (trim($e->nodeValue)>0 && is_numeric(trim($e->nodeValue))) {
				$temp3[] = trim($e->nodeValue);
			}
		}
		$r_consolation = $temp3;

		$data = array(
			'date' => $date,
			'draw'=>$drawid,
			'r_1' => $r_4d[0],
			'r_2' => $r_4d[1],
			'r_3' => $r_4d[2],
			'r_4' => $r_special,
			'r_5' => $r_consolation
		);
		return $this->CI->clotterymalaysia->singapore_4d_save($data);
	}

	function _get_singapore_jackpot($html) {
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		$xpath = new DOMXPath($doc);

		//SINGAPORE TOTO
		$element = $xpath->query('//font[text()="SINGAPORE TOTO"]');
		if(!$element || $element->length==0){
			return false;
		}
		$title = $element->item(0)->nodeValue;
		$date = $xpath->query('./ancestor::strong[1]/font[2]', $element->item(0))->item(0)->nodeValue;
		$date = preg_replace("#.*([0-9]{2})-([0-9]{1,2})-([0-9]{4}).*#i", "$3-$2-$1", $date);
		$date = date("Y-m-d", strtotime($date));
		$drawid = $xpath->query('./ancestor::strong[1]/font[3]', $element->item(0))->item(0)->nodeValue;
		$drawid = preg_replace("#[^\d/]#smiU", "", $drawid);
		$temp = $xpath->query('./ancestor::table[2]//tr[@class="linesbox"]//td', $element->item(0));
		$temp2 = array();
		foreach ($temp as $e) {
			if (trim($e->nodeValue)>0) {
				$temp2[] = trim(preg_replace('#[^\d]*#iU', '', $e->nodeValue));
			}
		}
		$r_645 = implode(",", $temp2);

		$data = array(
			'date' => $date,
			'draw'=>$drawid,
			'r_645' => $r_645
		);
		return $this->CI->clotterymalaysia->singapore_645_save($data);
	}

}

?>

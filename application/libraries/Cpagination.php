<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CPagination
{
	var $total = 0;	
	var $size = 5;	
    var $default_page_limit = 20;
	var $querystring = array();
	var $query_var = array();
	
	function  __construct() {        
		$this->querystring = array(
			"page_limit" => "page_limit",
			"cur_page" => "cur_page",
			"order_status" => "os",
			"order_by" => "ob",
			"filter_value" => "fv",
			"filter_field" => "ff",
			"filter_opt" => "fo",
		);
		$this->CI =& get_instance();	
        $this->CI->load->model('mpagination');
		$this->_get_default_query_var();		
	}
	
	function _get_default_query_var(){
		foreach($this->querystring as $value){
			$temp = (ISSET($_GET[$value])?$_GET[$value]:"");			
			$this->query_var[$value] = strtolower($temp);
		}		
	}
	
	function get_var($name){
		if(isset($this->query_var[$name])){
			return $this->query_var[$name];
		}
		return false;
	}
	
	function get_total(){
		return $this->total;
	}
	
	function set_total($i){
		if(!is_numeric($i)){
			$i=1;
		}
		$this->total = $i;
	}
	
	function get_count_start(){
		$cur_page = $this->get_current_page();
		$limit = $this->get_page_limit();
		$result = ($limit*($cur_page-1));	
		$result += 1;	//change 0 become 1
		if($result>0){
			return $result;
		}
		return false;
	}
	
	function get_current_page(){
		$temp = $this->get_var($this->querystring['cur_page']);
		if(!is_numeric($temp)){
			$temp = 1;
		}
		return $temp;
	}
	
	function get_filter_query(){
		$fields = $this->get_var($this->querystring['filter_field']);
		$value = $this->get_var($this->querystring['filter_value']);
		$opt = $this->get_var($this->querystring['filter_opt']);
		$temp = array();
		if(strlen($fields)>0 && strlen($value)>0){
			if(stripos($fields, ",")){
				preg_match_all("#\{(.*)\},#Ui", $fields, $matches);
				if(isset($matches[1])){
					$fields = $matches[1];
				}else{
					$fields = null;
				}
				preg_match_all("#\{(.*)\},#Ui", $value, $matches);
				if(isset($matches[1])){
					$value = $matches[1];
				}else{
					$value = null;
				}
				for($i=0; $i<sizeof($fields); $i++){
					$ffv = urldecode($fields[$i]);
					$fvv = urldecode($value[$i]);
					if(isset($ffv) && isset($fvv) && strlen($ffv)>0 && strlen($fvv)>0){						
						$t = explode(" ",$fvv,2);
						if(sizeof($t)==2 && array_search(strtolower($t[0]), array("=",">",">=","<","<=","like","in","is"))!==FALSE){
							$temp[] = '`'.$ffv.'` '.$fvv;
						}else{
							$temp[] = '`'.$ffv.'` LIKE "%'.$fvv.'%"';
						}
					}					
				}
			}else{				
				preg_match_all("#\{([^\}]*)\}#Ui", $fields, $matches);
				if(isset($matches[1])){
					$fields = $matches[1];
				}else{
					$fields = null;
				}
				foreach($fields as $k1){
					$temp[] = '`'.urldecode($k1).'` LIKE "%'.urldecode($value).'%"';
				}
			}
		}		
		if(sizeof($temp)>0){
			if($opt && strtolower($opt)=="and"){
				return implode(" AND ",$temp);
			}else{
				return implode(" OR ",$temp);
			}			
		}
		return false;
	}
	
	function get_page_limit(){
		if($this->get_var($this->querystring['page_limit'])){
			$temp = $this->get_var($this->querystring['page_limit']);
			$this->set_page_limit($temp);
			unset($this->query_var['page_limit']);
		}else{
			$temp = (ISSET($_SESSION[$this->querystring['page_limit']])?$_SESSION[$this->querystring['page_limit']]:null);
			IF(empty($temp)){
				$temp = $this->default_page_limit;
				$temp = (!empty($temp)?$temp:20);
				$this->set_page_limit($temp);
			}
		}			
		return $temp;
	}
	
	function set_page_limit($i){
		$_SESSION[$this->querystring['page_limit']] = $i;
	}
	
	function get_link($param = array()){
		static $link = "";		
		if(strlen($link)==0){
			$temp = explode("?",$_SERVER['REQUEST_URI'],2);			
			$query = array();			
			$link = $temp[0];
			if(isset($temp[1]) && strlen(trim($temp[1]))>0){
				$temp = explode("&",$temp[1]);
				foreach($temp as $key => $value){
					$temp2 = explode("=",$value,2);
					if(sizeof($temp2)==2){
						if(array_search($temp2[0], $this->querystring)===false){
							$query[] = $value;
						}
					}
				}
			}
			$link = $link."?".implode("&",$query);
		}
		$temp = "";
		$param = array_merge($this->query_var,$param);
		foreach($param as $key => $value){
			if(strlen(trim($value))>0){
				$temp .= "&".$key."=".$value;
			}			
		}		
		return $link.$temp;
	}
	
	function _link($cur_page = 1){
		return $this->get_link(array($this->querystring['cur_page']=>$cur_page));
	}
	
	function get_view(){		
		$data = $this->get_data();
		if(sizeof($data['pages'])>1){            
            return $this->CI->load->view('system/pagination_default',array("list"=>$data),TRUE);
		}
		return false;
	}
	
	function get_data(){
		$page = array();
		$size = $this->size;
		$begin = $size;
		$end = $size;
		$page['total'] = $this->get_total();
		$page['limitstart'] = $this->get_current_page();
		$page['limit'] = $this->get_page_limit();
		$page['totalpage'] = ceil($page['total'] / $page['limit']);
		//$page['current'] = ceil(($page['limitstart'] + 1) / $page['limit']);
		$page['current'] = $page['limitstart'];
		$page['pages'] = array();
		$page['prev'] = array();
		$page['next'] = array();
		$page['begin'] = array();
		$page['end'] = array();

		if($page['totalpage']>0){
			//prev
			$t = array();
			$t['text'] = "prev";
			if($page['current']-1<=0){
				$t['active'] = false;
			}else{
				$t['active'] = true;
				$t['link'] = $this->_link(($page['current']-1));
			}
			$page['prev'] = $t;

			//next
			$t = array();
			$t['text'] = "next";
			if($page['current']+1>$page['totalpage']){
				$t['active'] = false;
			}else{
				$t['active'] = true;
				$t['link'] = $this->_link(($page['current']+1));
			}
			$page['next'] = $t;


			if($page['totalpage']>=(($size*2)+5)){
				$value = $page['current'];
				if($value<=($size+2)){
					$begin = $value-1;
					if(($size*2)-$begin>=$size){
						$end = ($size*2)-$begin;
					}
				}else if($value>($page['totalpage']-($size+2))){
					$end = $page['totalpage']-$value;
					if(($size*2)-$end>=$size){
						$begin = ($size*2)-$end;
					}
				}

				//begin
				$page['begin']['text'] = "1";
				if(($value-$size-2)>0){
					$page['begin']['active'] = true;
					$page['begin']['link'] = $this->_link(1);
				}else{
					$page['begin']['active'] = false;
				}

				for($i=1; $i<=$begin; $i++){
					$temp = ($value-1-$begin+$i);
					if($temp<=$page['totalpage']){
                        if($i==1 && $temp>1){
                            $page['begin']['text2'] = $temp-1;
                            $page['begin']['link2'] = $this->_link(($temp-1));
                        }
						$t = array();
						if($temp==$page['current']){
							$t['active'] = false;
							$t['selected'] = true;
						}else{
							$t['active'] = true;
							$t['link'] = $this->_link(($temp));
						}
						$t['text'] = $temp;
						$page['pages'][$temp] = $t;
					}else{
						break;
					}
				}
				$t = array();
				$t['active'] = false;
				$t['selected'] = true;
				$t['text'] = $value;
				$page['pages'][$value] = $t;
				for($i=1; $i<=$end; $i++){
					$temp = ($value+$i);
					if($temp<=$page['totalpage']){
                        if($i==$end && $temp<$page['totalpage']){
                            $page['end']['text2'] = $temp+1;
                            $page['end']['link2'] = $this->_link(($temp+1));
                        }
						$t = array();
						if($temp==$page['current']){
							$t['active'] = false;
							$t['selected'] = true;
						}else{
							$t['active'] = true;
							$t['link'] = $this->_link(($temp));
						}
						$t['text'] = $temp;
						$page['pages'][$temp] = $t;
					}else{
						break;
					}
				}

                //end
                $page['end']['text'] = $page['totalpage'];
				if(($value+$size+2)<=$page['totalpage']){
					$page['end']['active'] = true;
                    $page['end']['link'] = $this->_link(($page['totalpage']));
				}else{
                    $page['end']['active'] = false;
                }

			}else{
				for($i=1; $i<=$page['totalpage']; $i++){
                    $temp = $i;
					if($temp<=$page['totalpage']){
						$t = array();
						if($temp==$page['current']){
							$t['active'] = false;
							$t['selected'] = true;
						}else{
							$t['active'] = true;
							$t['link'] = $this->_link(($temp));
						}
						$t['text'] = $temp;
						$page['pages'][$temp] = $t;
					}else{
						break;
					}
				}
			}

			return $page;
		}

		return false;
	}
	
	function query_limit($query='',$db_group_name = ''){		
		$temp = $this->get_filter_query();
		if($temp!==false && strlen($temp)>0){
			$query = ' SELECT * FROM ('.$query.') a WHERE 1=1 and ( '.$temp.' ) ';
		}
		$result = $this->CI->mpagination->get_count($query,$db_group_name);
		if(ISSET($result[0]['counting'])){
			$this->set_total($result[0]['counting']);
		}
		$cur_page = $this->get_current_page();
		$limit = $this->get_page_limit();
		$os = $this->get_var($this->querystring['order_status']);
		$ob = $this->get_var($this->querystring['order_by']);
		if(strlen(trim($os))>0 && strlen(trim($ob))>0){			
			$query = ' SELECT * FROM ('.$query.') a ORDER BY `'.$ob.'` '.$os;
		}		
		$query = $query." LIMIT ".($limit*($cur_page-1))." , ".$limit;
		return $query;
	}
	
}

?>
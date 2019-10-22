<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Require:
 *		cpagination
 */

class CListTemplate{
	
	var $filter_fields;
	var $core;
	var $hidden;
	var $action;
	var $ajax_action;
	var $base_url;	
	
	function  __construct() {
		$this->CI =& get_instance();
        $this->CI->cpage->set_javascript("myScript_JPopup.js");
		$this->CI->cpage->set_javascript("myScript_Jax.js");		
		$this->CI->cpage->set_javascript("listTemplate.js");
		$this->CI->cpage->set_stylesheet("listTemplate.css");
		$this->CI->load->library('cpagination');
		$this->CI->lang->load('menu');
		$this->set_base_url();
		$this->set_hidden(array('id','core'));
		$this->set_ajax_action(array());
		$this->set_action(array());
		$this->set_core(array());
		$this->set_filter(array());		
	}
    
    function get_default_base_url(){
        $url_array = $this->CI->uri->rsegments;
        array_pop($url_array);
        return "/".implode("/",$url_array);
    }
    
    function set_base_url($url = null){
		$this->base_url = array();
		$this->base_url['all'] = $this->get_default_base_url();
		if(is_array($url)){
			foreach($url as $a => $v){
				$this->base_url[$a] = $v;
			}
		}		
    }	
	
	function get_base_url($action = 'all'){
		$param = func_get_args();
		$param = array_slice($param, 1);
		$url = "";
		if(ISSET($this->base_url[$action])){
			$url = $this->base_url[$action];
		}else{
			$url = $this->base_url['all']."/".$action;
		}
		if(stripos($url, "javascript:")!==FALSE){
			if(sizeof($param)>0){
				foreach($param as $key => $value){
					$subject[] = "/\\$".$key."/i";
					$replacement[] = $value;
				}
				$url = preg_replace($subject,$replacement,$url);
			}
		}else{
			if(sizeof($param)>0){
				foreach($param as $key => $value){
					$url = $url."/".$value;
				}					
			}				
		}
		
		$url = preg_replace("#/[/]+#i", "/", $url);
		return $url;
	}
	
	function set_filter($key){
		$this->filter_fields = array();
		if(is_array($key)){
			foreach($key as $value){
				$this->filter_fields[$value] = $value;
			}
		}else{
			$this->filter_fields[$key] = $key;
		}		
	}
	
	function get_filter_query(){
		return implode("|",$this->filter_fields);
	}
	
	function set_core($key){
		$this->core = array();
		if(is_array($key)){
			foreach($key as $value){
				$this->core[$value] = $value;
			}
		}else{
			$this->core[$key] = $key;
		}		
	}
	
	function set_hidden($key){
		$this->hidden = array();
		if(is_array($key)){
			foreach($key as $value){
				$this->hidden[$value] = $value;
			}
		}else{
			$this->hidden[$key] = $key;
		}		
	}
	
	function set_action($key){
		$this->action = array();
		if(is_array($key)){
			foreach($key as $a => $b){
				if(is_numeric($a)){
					if(is_array($b)){
						$this->action[$b['field']]['field'] = $b['field'];
						$this->action[$b['field']]['fcompare'] = $b['fcompare'];
						$this->action[$b['field']]['fparam'] = $b['fparam'];
					}else{
						$this->action[$b]['field'] = $b;
					}
				}else{
					$this->action[$a]['field'] = $a;
					if(is_array($b)){						
						$this->action[$a]['fcompare'] = $b['fcompare'];
						$this->action[$a]['fparam'] = $b['fparam'];
					}
				}
			}
		}else{
			$this->action[$key]['field'] = $key;
		}		
	}
	
	function set_ajax_action($key){
		$this->ajax_action = array();
		if(is_array($key)){
			foreach($key as $a => $b){
				if(is_numeric($a)){
					if(is_array($b)){
						$this->ajax_action[$b['field']]['field'] = $b['field'];
						$this->ajax_action[$b['field']]['fcompare'] = $b['fcompare'];
						$this->ajax_action[$b['field']]['fparam'] = $b['fparam'];
						$this->ajax_action[$b['field']]['ftype'] = $b['ftype'];
					}else{
						$this->ajax_action[$b]['field'] = $b;
					}
				}else{
					$this->ajax_action[$a]['field'] = $a;
					if(is_array($b)){						
						$this->ajax_action[$a]['fcompare'] = $b['fcompare'];
						$this->ajax_action[$a]['fparam'] = $b['fparam'];
						$this->ajax_action[$a]['ftype'] = $b['ftype'];
					}else{
						$this->ajax_action[$a]['ftype'] = $b;
					}
				}
			}
		}else{
			$this->ajax_action[$key] = array('field'=>$key);
		}		
	}

	function get_list($data, $listname = "default_list"){
		if(empty($data) || !is_array($data)){
			$this->CI->load->library('cmessage');				
			$this->CI->cmessage->set_response_message("Data not found!","warning",'admin/page/user/list_view');				
			return false;
		}		
		$result = $this->_create_data($data);
				
        return $this->CI->load->view('system/'.$listname,array("data"=>$result),TRUE);
	}
    
    function _create_data($data){
		if(!ISSET($data['data']) || $data['data']==false){
			return false;
		}
		$result = array();	
        
        $temp = array();
		if(ISSET($data['header_allow'])){
			foreach($data['header_allow'] as $key){
				if(array_key_exists($key, $data['data'][0])!==FALSE && array_search($key, $this->hidden)===false){
					if(ISSET($data['header_name'][$key])){
						$temp[$key] = $data['header_name'][$key];
					}else{
						$temp[$key] = $key;
					}
				}
			}
		}else{
			foreach($data['data'][0] as $key => $value){
				if(array_search($key, $this->hidden)===false){
					if(ISSET($data['header_name'][$key])){
						$temp[$key] = $data['header_name'][$key];
					}else{
						$temp[$key] = $key;
					}
				}
			}	
		}
		
		foreach($temp as $key => $value){
			$ob = $this->CI->cpagination->get_var('ob');			
			$os = $this->CI->cpagination->get_var('os');
			$os = (($ob==$key && $os=="asc")?"desc":"asc");
			$im = 0;
			if(ISSET($data['header_img'])){
				$im = ((array_search($key,$data['header_img'])!==false)?1:0);
			}
			$result['header'][] = array(				
				"name"=>$key,
				"desc"=>$this->CI->lang->line($value),
				"order_link"=>$this->CI->cpagination->get_link(array("ob"=>$key,"os"=>$os)),
				"os"=>$os,
				"status"=>(($ob==$key)?1:0),
				"im"=>$im
			);
		}
		$this->set_filter(array_keys($temp));
		$result['filter_fields'] = $this->get_filter_query();
		$result['delete_link'] = $this->get_base_url('delete');
		
		foreach($data['data'] as $value){
			$temp = array();			
			$temp['data'] = (array)$value;
									
			if(ISSET($value['id']) && ISSET($this->core[$value['id']])){
				$temp['data']['core'] = 1;
			}
			foreach($this->ajax_action as $a => $b){
				if((strlen($b['fcompare'])==0 || $value[$b['fcompare']]=='1')){
					$param = array();
					if(sizeof($b['fparam'])>0){
						foreach($b['fparam'] as $v){
							if(ISSET($value[$v])){
								$param[$v] = $value[$v];
							}
						}
					}
					if(ISSET($value['id'])){
						$param['id'] = $value['id'];
					}
					$temp['ajax_action'][$b['field']] = array(					
						"name"=>$b['field'],
						"desc"=>$this->CI->lang->line($b['field']),					
						"link"=>call_user_method_array("get_base_url", $this, array_merge(array($b['field']),array_values($param))),
						"compare"=>(strlen($b['fcompare'])>0)?$b['fcompare']:"",
						"type"=>(strlen($b['ftype'])>0)?$b['ftype']:"func"
					);	
				}				
			}				
			foreach($this->action as $a => $b){
				$param = array();
				if(sizeof($b['fparam'])>0){
					foreach($b['fparam'] as $v){
						if(ISSET($value[$v])){
							$param[$v] = $value[$v];
						}
					}
				}
				if(ISSET($value['id'])){
					$param['id'] = $value['id'];
				}
				$temp['action'][$b['field']] = array(					
					"name"=>$b['field'],
					"desc"=>$this->CI->lang->line($b['field']),
					"link"=>call_user_method_array("get_base_url", $this, array_merge(array($b['field']),array_values($param))),
					"compare"=>(strlen($b['fcompare'])>0)?$b['fcompare']:"",
					"type"=>0
				);					
			}
			$result['records'][] = $temp;		
		}
				
        return $result;
	}
}

?>
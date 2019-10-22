<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	header('Content-Type: text/html; charset=UTF-8');

	$CI =& get_instance();
	$contents = "";
	
	/*
	 * set html title
	 */
	$temp = $CI->cpage->get_page_title();
	$temp = $CI->config->item('site_name').((strlen($temp)>0)?" :: ".$temp:"");	
	if(strlen($temp)>0){
		$contents .= '
			';		
		$contents .= '<TITLE>'.$temp.'</TITLE>';
	}		
	
	/*
	 * set meta
	 */
	$keywords = $CI->cpage->get_html_keywords();
	$desc = $CI->cpage->get_html_description();
	$contents .= '
			';
	$contents .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';	
	if(strlen($keywords)>0){
		$contents .= '
			';
		$contents .= '<meta name="keywords" content="" />';		
	}
	if(strlen($desc)>0){
		$contents .= '
			';
		$contents .= '<meta name="description" content="" />';
	}		
		
	/*
	 * set stylesheet
	 */	
	foreach($CI->cpage->get_stylesheet() as $key => $value){
		if(is_file($value)){

		}else if(is_file(APPPATH."templates/".$CI->cpage->get_layout()."/css/".$value)){
			$value = "/".APPPATH."templates/".$CI->cpage->get_layout()."/css/".$value;
		}else if(is_file(APPPATH."globals/css/".$value)){
			$value = "/".APPPATH."globals/css/".$value;
		}
		$contents .= '
			';		
		$contents .= '<link rel="stylesheet" type="text/css" href="'.$value.'" />';
	}

	/*
	 * set javascript
	 */
	//$CI->cpage->set_javascript("https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js");    
	$contents .= '
			';
	$contents .= '<script type="text/javascript" src="/'.APPPATH.'globals/js/jquery-1.6.1.min.js"></script>';
	foreach($CI->cpage->get_javascript() as $key => $value){
		if(is_file($value)){

		}else if(is_file(APPPATH."templates/".$CI->cpage->get_layout()."/js/".$value)){
			$value = "/".APPPATH."templates/".$CI->cpage->get_layout()."/js/".$value;
		}else if(is_file(APPPATH."globals/js/".$value)){
			$value = "/".APPPATH."globals/js/".$value;
		}
		$contents .= '
			';
		$contents .= '<script type="text/javascript" src="'.$value.'"></script>';
	}
	
	// Date in the past
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

	// always modified
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

	// HTTP/1.1
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);

	// HTTP/1.0
	header("Pragma: no-cache");
		
	echo $contents;
?>
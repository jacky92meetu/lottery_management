<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cuser_points{
	
    function  __construct() {
		$this->CI =& get_instance();		
		$this->CI->load->model('muser_points');
		$this->CI->load->library('ccfg');
	}
		
	function credits_price_conversion(){
		$points = $this->CI->ccfg->get('credits_price_conversion');
		if(!is_numeric($points)){
			$points = 0;
		}
		return $points;
	}
	
	function credits_refered_user_topup(){
		$points = $this->CI->ccfg->get('credits_refered_user_topup');
		if(strlen($points)==0){
			$points = 0;
		}
		return $points;
	}
	
	function credits_topup_extra(){
		$points = $this->CI->ccfg->get('credits_topup_extra');
		if(strlen($points)==0){
			$points = 0;
		}
		return $points;
	}
	
	function credits_refered_user_activation(){
		$points = $this->CI->ccfg->get('credits_refered_user_activation');
		if(!is_numeric($points)){
			$points = 0;
		}
		return $points;
	}
	
	function credits_new_user(){
		$points = $this->CI->ccfg->get('credits_new_user');
		if(!is_numeric($points)){
			$points = 0;
		}
		return $points;
	}
	
	function get_user_points($cid = null){
		if(is_null($cid)){
			$this->CI->load->library('cuser');
			$user =& $this->CI->cuser->getLoginUser();
			$cid = $user->id;
		}
		$points = 0;
		$result = $this->CI->muser_points->get_points_by_userid($cid);
		if($result && isset($result[0]['points'])){
			$points = $result[0]['points'];
		}
		
		return $points;
	}
		
	function get_product_points($product){
		$this->CI->load->library('clottery');
		$points = 0;
		$result = $this->CI->mlottery->get_product($product);
		if($result && $result[0]['cprice']>0){
			$points = $this->get_points_from_currency($result[0]['cprice']);
		}		
		return $points;
	}
	
	function get_refered_user_topup($price = 0){		
		$rate = $this->credits_refered_user_topup();
		$points = 0;
		if(stripos($rate, "%")!==FALSE){
			$rate = str_ireplace("%", '', $rate);
			$rate = $rate/100;
			$points = $price * $rate;
		}else{
			$points = $rate;
		}		
		return $points;
	}
	
	function get_topup_extra($price = 0){		
		$rate = $this->credits_topup_extra();
		$points = 0;
		if(stripos($rate, "%")!==FALSE){
			$rate = str_ireplace("%", '', $rate);
			$rate = $rate/100;
			$points = $price * $rate;
		}else{
			$points = $rate;
		}		
		return $points;
	}
	
	function get_points_from_currency($price = 1){
		$rate = $this->credits_price_conversion();
		return $price * $rate;		
	}
	
	function get_product_display_points($product){
		return $this->number_format($this->get_product_points($product));
	}
	
	function get_display_points($cid = null){
		return $this->number_format($this->get_user_points($cid));
	}
	
	function number_format($points){
		return number_format($points, 2, '.', ',');		
	}
	
	function credits_transfer($from,$to,$amount){
		$this->CI->load->library('cuser');
		$from_user = $this->CI->cuser->getUserByUsername($from);
		$to_user = $this->CI->cuser->getUserByUsername($to);
		if($from_user->id!=0 && $to_user->id!=0){
			$points = $this->get_user_points($from_user->id);
			if($points>=$amount){
				$this->CI->muser_points->points_add(($amount*-1),"Transfer ".$this->CI->lang->line('default_points_name')." to ".$to_user->username,$from_user->id);
				$this->CI->muser_points->points_add($amount,"".$this->CI->lang->line('default_points_name')." received from ".$from_user->username,$to_user->id);
				return true;
			}
		}
		
		return false;
	}
}

?>
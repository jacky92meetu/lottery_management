<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CLottery{
	
    function  __construct() {
		$this->CI =& get_instance();	
		$this->CI->load->model('mlottery');
	}
	
/*
 * FUNCTION LIST
 */		
	function show_mresult_group($id = null){
		$contents = '';
		$result = $this->CI->mlotteryresource->get_mresult_list();
		if($result){
			foreach($result as $value){
				if($value['cstatus']==1){
					$contents .= '<OPTION value="'.$value['cname'].'" ';
					if(!is_null($id)){
						if($id==$value['cname']){
							$contents .= 'SELECTED';
						}					
					}else if($value['default']==1){
						$contents .= 'SELECTED';
					}
					$contents .= '>'.$value['cdesc'].'</OPTION>';
				}
			}
		}
		if(strlen($contents)>0){
			$contents = '<SELECT name="form_group">'.$contents;
			$contents = $contents.'</SELECT>';
		}
		return $contents;
	}
	
	function pmobile_add($cid,$phone,$product,$status=0,$qty=1){
		
		//mnc check for status = 0
		if($status == 0){
			$status = 7;
		}
		
		if(!is_numeric($qty) || $qty<=0){
			return false;
		}
		
		$this->CI->load->library('clottery_sms');
		$phone = $this->CI->clottery_sms->format_phone($phone);
		if($phone){			
			$p = $this->CI->mlottery->get_product($product);			
			if($p){
				$exists = 1;
				$pmobile = $this->CI->mlottery->get_pmobile_by_phone($phone,$product);
				if(!$pmobile){
					$exists = 0;
					$result = $this->CI->mlottery->pmobile_add($cid,$phone,$product,'','0');
					$pmobile = $this->CI->mlottery->get_pmobile_by_phone($phone,$product);
				}
				$this->CI->mlottery->pmobile_update_status($phone,$status,$product);
				$pmobile = $this->CI->mlottery->get_pmobile_by_phone($phone,$product);
								
				$date = date("Y-m-d");
				if($pmobile && $p[0]['ctype']=="2"){
					$date = $p[0]['cfixed_date'];
				}else if($pmobile && ($p[0]['ctype']=="0" || $p[0]['ctype']=="1")){
					if($pmobile && $p[0]['ctype']=="1"){
						$d1 = $pmobile[0]['cexpire_date'];
						if(strtotime($d1)!=0 && strtotime($d1) > strtotime($date)){							
							$date = date("Y-m-d",strtotime($d1));
						}
					}					
					if($p[0]['cmonth']>0){
						$date = date("Y-m-d", strtotime($date." +".$p[0]['cmonth']*$qty." month "));
					}
					if($p[0]['cday']>0){
						$date = date("Y-m-d", strtotime($date." +".$p[0]['cday']*$qty." day "));
						if(!$exists){
							$date = date("Y-m-d", strtotime($date." -1 day "));
						}
					}					
					if(strtotime($p[0]['cfixed_date'])!=0 && strtotime($date)> strtotime($p[0]['cfixed_date'])){
						$date = $p[0]['cfixed_date'];
					}
				}
				
				$datea = date("Y-m-d");
				$dateb = date("Y-m-d",strtotime($date));
				if(strtotime($datea) > strtotime($dateb)){
					return false;
				}
					
				$this->CI->mlottery->pmobile_update_expire($phone,$date,$product);				
				
				$pmobile = $this->CI->mlottery->get_pmobile_by_phone($phone,$product);
				$this->CI->mlottery->subscribe_set($pmobile[0]['id'],$cid,$p[0]['id'],$qty);
				$this->CI->clottery_sms->process_sms_send_by_pcode($product,$datea,$phone);
				return $pmobile[0];
			}
		}
		return false;
	}
	
	function pmobile_del($phone,$product = null){
		$this->CI->load->library('clottery_sms');
		$phone = $this->CI->clottery_sms->format_phone($phone);
		if($phone){							
			return $this->CI->mlottery->pmobile_del($phone,$product);
		}
		return false;
	}
	
	function check_product_result($phone,$result){
		$date = date('Y-m-d');
		$this->CI->load->library('clottery_sms');
		$phone = $this->CI->clottery_sms->format_phone($phone);
		$pmobile = $this->CI->mlottery->get_pmobile_by_phone($phone);
		if($pmobile && sizeof($pmobile)>0){
			foreach($pmobile as $value){
				if(strtotime($date)<=strtotime($value['cexpire_date'])){			
					$p = $this->CI->mlottery->get_product($value['cproduct']);
					if($p){
						$cmd = explode("\r\n",$p[0]['cresult']);
						$taskname = "[".$result."]";				
						if(array_search($taskname, $cmd)!==FALSE){
							return true;
						}
					}
				}
			}
		}
		return false;
	}
	
	function check_product_expire($phone,$product){				
		$date = date('Y-m-d');
		$this->CI->load->library('clottery_sms');
		$phone = $this->CI->clottery_sms->format_phone($phone);
		$pmobile = $this->CI->mlottery->get_pmobile_by_phone($phone,$product);		
		if($pmobile && sizeof($pmobile)>0){
			foreach($pmobile as $value){
				if(strtotime($date)<=strtotime($value['cexpire_date'])){			
					return true;
				}
			}
		}
		return false;
	}
	
	function show_product($name = null){
		$contents = '';
		$group = $this->CI->mlottery->get_product('',1);
		if(isset($group[0])){			
			foreach($group as $key => $value){
				$contents .= '<OPTION value="'.$value['cname'].'" ';
				if($name==$value['cname']){
					$contents .= 'SELECTED';
				}					
				$contents .= '>'.$value['cname'].' - '.$value['cdesc'].'</OPTION>';
			}			
		}
		if(strlen($contents)>0){
			$contents = '<SELECT name="form_group">'.$contents;
			$contents = $contents.'</SELECT>';
		}
		return $contents;
	}
	
	function register_success_sms($phone,$product,$expire_date){		
		$this->CI->load->library('clottery_sms');
		$this->CI->lang->load('sms');
		$p = $this->CI->mlottery->get_product($product);
		if(array_search($p[0]['cpublish'], array("1","2"))!==FALSE){
			$text = $this->CI->lang->line('SMS_REGISTER_SUCCESS',$p[0]['cdesc'],date("d/m/Y",strtotime($expire_date)));
			return $this->CI->clottery_sms->send_sms("", $phone, $text);		
		}
		return false;
	}
	
	function expired_sms($phone,$product,$expire_date){
		$start_date = date("Y-m-d").' 10:00:00';
		$this->CI->load->library('clottery_sms');
		$this->CI->lang->load('sms');
		$p = $this->CI->mlottery->get_product($product);
		if(array_search($p[0]['cpublish'], array("1","2"))!==FALSE){
			$text = $this->CI->lang->line('SMS_EXPIRE',$p[0]['cdesc']);
			return $this->CI->clottery_sms->send_sms("", $phone, $text,$start_date);
		}
		return false;
	}
	
	function vc_sms($phone,$vc){		
		$this->CI->load->library('clottery_sms');
		$this->CI->lang->load('sms');
		$text = $this->CI->lang->line('SMS_VC',$vc);
		return $this->CI->clottery_sms->send_sms("", $phone, $text);
	}
}
?>

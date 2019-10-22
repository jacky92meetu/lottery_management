<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CLottery_smsnow_2way{
	
    function  __construct() {
		$this->CI =& get_instance();
		$this->CI->load->library('clottery_sms');		
	}
	
/*
 * 2way process
 */	
	function init_2way(){
		if(!isset($_GET['s']) || !isset($_GET['r']) || !isset($_GET['t'])){
			return false;
		}
		$this->CI->load->library('clottery');
		if($_GET['s']!="22099" && $this->CI->clottery_sms->digit_phone($_GET['s'])===FALSE){
			return false;
		}
		$result = false;
		$data['sender'] = $_GET['s'];
		$data['recipient'] = $_GET['r'];
		$data['text'] = $_GET['t'];
		$data['cmd_list'] = explode(" ",strtolower(trim($data['text'])));
		$task = $data['cmd_list'][0];		
		$this->CI->load->library('cuser');
		$this->CI->clottery_sms->save_sms_inbox($data['sender'], $data['recipient'], $data['text']);		
		if($data['sender']=="22099"){
			$result = $this->proc_2way_mnc_update($data);
		}
		/*
		else if($task=="get"){			
			$result = $this->proc_2way_reg_free($data);
		}
		 * 
		 */
		else{
			$phone = $this->CI->clottery_sms->format_phone($data['sender']);
			if($phone){
				$user = $this->CI->muser->get_user_by_cmobile($phone);
				if($user){
					if($user[0]['block']==0){
						if($task=="bal"){
							$result = $this->proc_2way_get_balance($data);
						}else{
							if($user[0]['group']==2){
								switch ($task){
									case "r":
									case "result": $result = $this->proc_2way_get_result($data);
												break;
									case "jackpot": $result = $this->proc_2way_get_jackpot_result($data);
												break;
									case "reg": $result = $this->proc_2way_reg_phone($data);
												break;				
									case "del": $result = $this->proc_2way_del_phone($data);
												break;	
									case "start": $result = $this->proc_2way_start($data);
												break;	
									case "stop": $result = $this->proc_2way_stop($data);
												break;	
									case "s":
									case "search": $result = $this->proc_2way_search($data);
												break;	
									default: $result = $this->proc_2way_reg_phone_by_points($data);								
												break;
								}
							}else if($user[0]['group']==1){
								switch ($task){							
									default: $result = $this->proc_2way_reg_phone_by_points($data);								
												break;
								}
								if(!$result){}
							}
						}				
					}else{
						$this->CI->clottery_sms->send_sms("", $data['sender'], $this->CI->lang->line('SMS_INVALID_COMMAND'));
					}
				}					
			}				
		}
		return $result;
	}
	
	function proc_2way_reg_phone($data){		
		$this->CI->load->library('clottery_waiting_list');
		$code = $data['cmd_list'][1];
		$phone = $this->CI->clottery_sms->format_phone($data['cmd_list'][2]);
		$qty = 1;
		if(isset($data['cmd_list'][3])){
			$qty = $data['cmd_list'][3];
		}
		if($phone){
			$user = $this->CI->muser->get_user_by_cmobile($data['sender']);
			$p = $this->CI->mlottery->get_product_by_param(array("ccode"=>$code));
			if($user && $p){
				return $this->CI->clottery_waiting_list->waiting_list_add($phone,$p[0]['cname'],$qty,3,0,$user[0]['id']);
			}			
		}	
		return false;
	}
	
	function proc_2way_reg_phone_by_points($data){
		$this->CI->load->library('clottery_waiting_list');
		$code = $data['cmd_list'][0];
		$phone = $this->CI->clottery_sms->format_phone($data['cmd_list'][1]);
		$qty = 1;
		if(isset($data['cmd_list'][2])){
			$qty = $data['cmd_list'][2];
		}
		if($phone){
			$user = $this->CI->muser->get_user_by_cmobile($data['sender']);
			$p = $this->CI->mlottery->get_product_by_param(array("ccode"=>$code, "cpublish"=>"1"));
			if($user && $p){
				$cpoints = $this->CI->cuser_points->get_user_points($user[0]['id']);
				$total = $this->CI->cuser_points->get_product_points($p[0]['cname']) * $qty;
				if($total<=$cpoints){
					if($this->CI->muser_points->points_add(($total*-1),"Subscription for ".$phone." - ".$qty." X [".$p[0]['cdesc']."]",$user[0]['id'])){
						$this->CI->clottery_waiting_list->waiting_list_add($phone,$p[0]['cname'],$qty,3,0,$user[0]['id']);						
						$this->CI->clottery_sms->send_sms("", $data['sender'], $this->CI->lang->line('SMS_MOBILE_SUBSCRIBE',$phone,$p[0]['cdesc'],$qty,$this->CI->cuser_points->get_display_points($user[0]['id'])));
						return true;
					}
				}else{
					$this->CI->clottery_sms->send_sms("", $data['sender'], "Insufficient ".$this->CI->lang->line('default_points_name')."! Need ".$total." ".$this->CI->lang->line('default_points_name')." to proceed this transaction.");
					return false;
				}
			}
		}
		$this->CI->clottery_sms->send_sms("", $data['sender'], $this->CI->lang->line('SMS_INVALID_COMMAND'));
		return false;
	}	
	
	function proc_2way_reg_free($data){
		$this->CI->load->library('cuser');		
		$this->CI->load->library('clottery_waiting_list');
		$this->CI->load->model('mlog');
		$sms_text = "";
				
		$product = "lottery_foc_1";
		$user = $this->CI->cuser->getUserByUsername("freetesting");
		if($user->id!=0){
			$phone = $this->CI->clottery_sms->format_phone($data['sender']);
			if($phone){
				$date = date("Y-m-d");
				$result = $this->CI->mlottery_waiting_list->get_waiting_list_by_param(array('cphone_no'=>$phone,'cproduct'=>$product,'ctype'=>'1','ccreated_date'=>$date));
				if(!$result){
					$result = $this->CI->clottery_waiting_list->waiting_list_add($phone,$product,1,1,1,$user->id);
					if($result){						
						$sms_text = $this->CI->lang->line('SMS_SUBSCRIPTION_SUCCESS');
					}else{
						$sms_text = $this->CI->lang->line('SMS_SUBSCRIPTION_FAIL');
					}
				}else{
					$sms_text = $this->CI->lang->line('SMS_SUBSCRIPTION_SUCCESS');
				}
			}else{
				$sms_text = $this->CI->lang->line('SMS_NUMBER_ERROR');
			}
		}else{
			$sms_text = $this->CI->lang->line('SMS_FREE_OOS_ERROR');
		}
		
		if(strlen($sms_text)>0){
			$this->CI->mlog->add_log(array(
				"cip"=>$_SERVER['REMOTE_ADDR'],
				"ctype"=>"FREE2",
				"cremarks"=> $sms_text,
				"ctext"=>  json_encode($data)
			));
			//$this->CI->clottery_sms->send_sms("", $data['sender'], $sms_text);
		}
		return false;
	}
	
	function proc_2way_get_result($data){	
		$this->CI->load->library('clotteryresource');
		$this->CI->load->library('clottery_waiting_list');		
		$phone = $this->CI->clottery_sms->format_phone($data['sender']);
		if($phone){
			$this->CI->load->model('muser');
			$user = $this->CI->muser->get_user_by_cmobile($phone);
			if($user && strlen($data['cmd_list'][1])>0){
				$result = $this->CI->mlotteryresource->get_mresult_by_param(array("ccode"=>$data['cmd_list'][1]));
				if($result){
					$date = "";						
					if(isset($data['cmd_list'][2]) && strlen($data['cmd_list'][2])>0){
						$result3 = $this->CI->mlottery->get_pmobile_by_phone($phone);
						if($user[0]['group']==2 || $result3){}else{
							//$this->CI->clottery_sms->send_sms("", $data['sender'], $this->CI->lang->line('SMS_PAID_PLAN_ONLY'));
							return false;
						}							
						$date = $data['cmd_list'][2];
						preg_match_all('#([0-9]{1,2})[-/]{1}([0-9]{1,2})[-/]{1}([0-9]{4})#iU', $date, $matches);
						if(isset($matches[1][0]) && isset($matches[2][0]) && isset($matches[3][0]) && checkdate($matches[2][0], $matches[1][0], $matches[3][0])){
							$date = sprintf("%04d",$matches[3][0])."-".sprintf("%02d",$matches[2][0])."-".sprintf("%02d",$matches[1][0]);								
						}
					}
					$result2 = $this->CI->clottery_sms->process_sms_send($result[0]['cname'],$date,$phone);
					if($result2){
						return true;
					}else{
						//$this->CI->clottery_sms->send_sms("", $data['sender'], $this->CI->lang->line('SMS_GET_NOT_FOUND',$date));
						return false;
					}
				}
			}
			//$this->CI->clottery_sms->send_sms("", $data['sender'], $this->CI->lang->line('SMS_INVALID_COMMAND'));
		}
		return false;
	}
	
	function proc_2way_get_latest_result($data){	
		$this->CI->load->model('muser');
		$phone = $this->CI->clottery_sms->format_phone($data['sender']);
		if($phone){
			$user = $this->CI->muser->get_user_by_cmobile($phone);
			if($user){
				$this->proc_2way_get_result($data);
			}else{
				//$result = $this->CI->clottery_waiting_list->waiting_list_add($phone,"system_registration",1,4,0,0,json_encode($data));
				$digit_phone = $this->CI->clottery_sms->digit_phone($phone);				
				$reg = array(
					"username"=>$digit_phone,
					"password"=>"123456",
					"name"=>$digit_phone,
					"email"=>"",
					"group"=>"1",
					"block"=>"0",
					"cmobile"=>$phone
					);
				if($this->CI->muser->add_user($reg)){
					$this->proc_2way_get_latest_result($data);
				}
				return true;
			}			
		}
		return false;
	}
	
	function proc_2way_del_phone($data){
		$phone = $this->CI->clottery_sms->format_phone($data['cmd_list'][1]);		
		if($phone){
			$this->CI->load->library('clottery');
			if($this->CI->clottery->pmobile_del($phone)){
				$this->CI->clottery_sms->send_sms("", $data['sender'], $data['cmd_list'][1]." - Deleted!");
			}
		}
		return false;
	}
	
	function proc_2way_start($data){
		$this->CI->load->library('ccfg');
		if($this->CI->ccfg->set('cronjob_result_send','1')){
			$this->CI->clottery_sms->send_sms("", $data['sender'], "SMS send process start");
			return true;
		}
		return false;
	}
	
	function proc_2way_stop($data){
		$this->CI->load->library('ccfg');
		if($this->CI->ccfg->set('cronjob_result_send','0')){
			$this->CI->clottery_sms->send_sms("", $data['sender'], "SMS send process stop");
			return true;
		}
		return false;
	}
	
	function proc_2way_search($data){
		$num = $data['cmd_list'][1];
		$result = $this->CI->clottery_sms->process_sms_send('mkt_search_result',null,$data['sender']);
		if($result){
			return true;
		}		
		$this->CI->clottery_sms->send_sms("", $data['sender'], "No Record");
		return false;
	}
	
	function proc_2way_get_balance($data){
		$phone = $this->CI->clottery_sms->format_phone($data['sender']);
		$user = $this->CI->muser->get_user_by_cmobile($phone);
		if($user){			
			$this->CI->clottery_sms->send_sms("", $data['sender'], $this->CI->lang->line('SMS_MOBILE_BAL',$this->CI->cuser_points->get_display_points($user[0]['id'])));
			return true;
		}
		return false;
	}
	
	function proc_2way_mnc_update($data){		
		$phone = false;		
		preg_match_all("#^.*(01[0-9]{8,9}).*$#iU", $data['text'], $matches);
		if(isset($matches[1][0])){
			$phone = $this->CI->clottery_sms->format_phone($matches[1][0]);
		}
		if($phone){
			$telco = "";
			$success = 0;					
			if($data['sender']=="22099" && stripos($data['text'], "on net")!==FALSE){				
				$success = 1;
				$telco = "celcom";
			}else if($data['sender']=="22099" && stripos($data['text'], "off net")!==FALSE){				
				$success = 1;
				$telco = "";
			}
			if($success){
				//update user table
				$this->CI->load->model('muser');				
				$result = $this->CI->muser->get_user_by_param(array('cmobile'=>$phone,'group'=>'1','block'=>'1','cmobile_status'=>'0'));
				if($result && sizeof($result)>0){
					foreach($result as $value){						
						$this->CI->muser->update_user_by_param($value['id'],array('cmobile_status'=>'1'));
						if($value['block']=='1' && strlen($value['activation_code'])>0){
							$this->CI->clottery->vc_sms($value['cmobile'],$value['activation_code']);							
						}
					}
				}				
				
				//update pmobile table
				$result = $this->CI->mlottery->get_pmobile_by_phone($phone);
				if($result && sizeof($result)>0){
					foreach($result as $value){
						if($value['cstatus']=="10"){
							$this->CI->clottery->register_success_sms($value['cphone_no'],$value['cproduct'],$value['cexpire_date']);							
						}
					}
				}				
				$this->CI->mlottery_sms->mnc_update_status($phone,$telco,'1');
				
				//update lottery_waiting_list table
				$this->CI->load->library('clottery_waiting_list');
				$result = $this->CI->mlottery_waiting_list->get_waiting_list_by_pts($phone,null,'8');
				if($result && sizeof($result)>0){
					foreach($result as $value){
						if(array_search($value['ctype'], array('1','2','3'))!==FALSE){
							/*
							//celcom rebate process
							$user = $this->CI->muser->get_user($value['coid']);
							if($telco=="celcom" && ($user && $user[0]['id']>1)){
								$p = $this->CI->mlottery->get_product_by_param(array("cname"=>$value['cproduct']));
								if($p){
									$qty = $value['cqty'];									
									$total = ceil($this->CI->cuser_points->get_product_points($p[0]['cname']) * $qty * 0.6666);
									$this->CI->muser_points->points_add($total,"CELCOM Rebate for ".$phone." - ".$qty." X [".$p[0]['cdesc']."]",$value['coid']);
								}								
							}
							*/
							$this->CI->mlottery_waiting_list->waiting_list_update_status($value['id'],'5');
						}else{
							//$this->CI->mlottery_waiting_list->waiting_list_update_status($value['id'],'1');
						}
					}
				}
				$result = $this->CI->mlottery_waiting_list->get_waiting_list_by_param(array("cphone_no"=>$phone,"cproduct"=>"system_registration","cstatus"=>"8"));
				if($result && sizeof($result)>0){
					foreach($result as $value){
						if(array_search($value['ctype'], array('4'))!==FALSE){
							$digit_phone = $this->CI->clottery_sms->digit_phone($phone);
							$format_phone = $this->CI->clottery_sms->format_phone($phone);
							if($this->CI->muser->get_user_by_username($digit_phone) or $this->CI->muser->get_user_by_cmobile($format_phone)){
								$this->CI->clottery_sms->send_sms("", $data['sender'], $this->CI->lang->line('SMS_SUBSCRIBED_FAIL'));
							}else{
								$reg = array(
									"username"=>$digit_phone,
									"password"=>"123456",
									"name"=>$digit_phone,
									"email"=>"",
									"group"=>"3",
									"block"=>"0",
									"cmobile"=>$format_phone
									);
								if($this->CI->muser->add_user($reg)){
									if(strlen($value['cremarks'])>0){
										$temp = (array)json_decode($value['cremarks']);
										$this->proc_2way_get_latest_result($temp);
									}									
								}
							}
							$this->CI->mlottery_waiting_list->waiting_list_update_status($value['id'],'1');
						}
					}
				}
			}else{
				$this->CI->mlottery_sms->mnc_update_status($phone,$telco,'2');
			}
		}
		return true;
	}
		
}
?>

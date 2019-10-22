<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminControllerLottery {
	
    function __construct() {
		$this->CI =& get_instance();		
		$this->CI->load->library('clottery');		
	}	
	
	function index(){		
		$this->summary_view();
	}
	
	function summary_view(){		
		$data = $this->CI->input->get_form_data();
		if(ISSET($data['section']) && $data['section']=="delete"){
			if(ISSET($data) && sizeof($data)>0){
				$count = 0;				
				foreach($data as $key => $value){
					if(strtolower(substr($key,0,4))=="chk_"){
						$id = substr($key,4,strlen($key));					
						$result = $this->CI->mlotteryresource->get_history_by_id($id);
						if($result){
							$this->CI->mlotteryresource->history_delete($id);
							$data = $this->CI->mlotteryresource->result_delete($result[0]['cfrom'],$result[0]['cdate'],$result[0]['ctitle']);
							if($data){				
								$count += 1;
							}
						}
					}
				}
				if($count>0){
					$this->CI->load->library('cmessage');				
					$this->CI->cmessage->set_response_message("Result delete successfully","notice",$_SERVER['REQUEST_URI']);
				}				
			}
		}
		$this->CI->load->library('clisttemplate');
		$this->CI->cpage->set_page_title('Lottery Summary View');
		$data['data'] = $this->CI->mlotteryresource->web_get_lottery_summary();
		$this->CI->clisttemplate->set_ajax_action(array('cstatus','delete'));
		$this->CI->clisttemplate->set_base_url(array('cstatus'=>'ajax_lottery_status','delete'=>'ajax_lottery_delete'));
		$list = $this->CI->clisttemplate->get_list($data,"default_list");
		$this->CI->load->admin_view('default_list',array("list"=>$list));
	}
	
	function product_summary_view(){		
		$this->CI->load->library('clisttemplate');		
		$this->CI->cpage->set_page_title('Lottery Product Summary View');				
		$data['data'] = $this->CI->mlottery->web_get_mproduct_list();
		$list = $this->CI->clisttemplate->get_list($data,"default_list_xdelete");		
		$this->CI->load->admin_view('default_list',array("list"=>$list));
	}
	
	function msubscribe_summary_view(){		
		$this->CI->load->library('clisttemplate');		
		$this->CI->cpage->set_page_title('Lottery Subscribe Summary');
		$data['data'] = $this->CI->mlottery->web_get_msubscribe_list();
		$data['header_name'] = array("ctype"=>"Extendable");
		$list = $this->CI->clisttemplate->get_list($data,"default_list_xdelete");		
		$this->CI->load->admin_view('default_list',array("list"=>$list));
	}
	
	function msubscribe_group_view(){		
		$this->CI->load->library('clisttemplate');		
		$this->CI->cpage->set_page_title('Lottery Subscribe Group');
		$data['data'] = $this->CI->mlottery->web_get_msubscribe_group();		
		$list = $this->CI->clisttemplate->get_list($data,"default_list_xdelete");		
		$this->CI->load->admin_view('default_list',array("list"=>$list));
	}
	
	function msubscribe_user_summary_view(){		
		$this->CI->load->library('clisttemplate');		
		$this->CI->cpage->set_page_title('Lottery Subscription User Summary');
		$this->CI->clisttemplate->set_action(array('view'=>'dialog'));
		$this->CI->clisttemplate->set_base_url(array('view'=>'/admin/page/lottery/msubscribe_user_details_view'));
		$data['data'] = $this->CI->mlottery->web_get_msubscribe_user_summary_list();
		$list = $this->CI->clisttemplate->get_list($data,"default_list_xdelete");		
		$this->CI->load->admin_view('default_list',array("list"=>$list));
	}
	
	function msubscribe_user_details_view($id){
		$this->CI->load->library('clisttemplate');		
		$this->CI->cpage->set_page_title('Lottery Subscription User Summary');
		$this->CI->cpage->set_sub_menu(array("back"=>"/admin/page/lottery/msubscribe_user_summary_view/"));
		$data['data'] = $this->CI->mlottery->web_get_msubscribe_user_details_list($id);		
		$list = $this->CI->clisttemplate->get_list($data,"default_list_xdelete");		
		$this->CI->load->admin_view('default_list',array("list"=>$list));
	}
	
	function summary_result($table = "4d"){
		$this->CI->load->library('clisttemplate');		
		$this->CI->cpage->set_page_title('Lottery Summary '.  strtoupper($table).' Result');
		$this->CI->cpage->set_javascript("myScript_DValidation.js");
		
		$data['data'] = $this->CI->mlotteryresource->web_get_lottery_summary_result($table);
		$this->CI->clisttemplate->set_ajax_action(array('edit'=>'dialog'));
		$this->CI->clisttemplate->set_base_url(array('edit'=>'/admin/page/lottery/ajax_edit/'.$table));
		$list = $this->CI->clisttemplate->get_list($data,"default_list_xdelete");
		$this->CI->load->admin_view('default_list',array("list"=>$list));
	}
	
	function waiting_list_view(){
		$this->CI->load->library('clottery_waiting_list');
		$data = $this->CI->input->get_form_data();
		if(ISSET($data['section']) && $data['section']=="delete"){
			if(ISSET($data) && sizeof($data)>0){
				$count = 0;				
				foreach($data as $key => $value){
					if(strtolower(substr($key,0,4))=="chk_"){
						$id = substr($key,4,strlen($key));					
						$result = $this->CI->mlottery_waiting_list->waiting_list_delete($id);
						if($result){
							$count += 1;
						}
					}
				}
				if($count>0){
					$this->CI->load->library('cmessage');				
					$this->CI->cmessage->set_response_message("Result delete successfully","notice",$_SERVER['REQUEST_URI']);
				}				
			}
		}
		$this->CI->load->library('clisttemplate');		
		$this->CI->cpage->set_page_title('Lottery Waiting List Summary');
		$this->CI->clisttemplate->set_ajax_action(array('activate'=>'func'));
		$this->CI->clisttemplate->set_base_url(array('activate'=>'ajax_lottery_waiting_list_process'));
		$data['data'] = $this->CI->mlottery_waiting_list->web_get_waiting_list();		
		$list = $this->CI->clisttemplate->get_list($data,"default_list");		
		$this->CI->load->admin_view('default_list',array("list"=>$list));
	}
	
	function lottery_result_by_date(){
		$this->CI->load->library('clisttemplate');		
		$this->CI->cpage->set_page_title('Lottery Result By Date');		
		
		$data['data'] = $this->CI->mlotteryresource->web_get_lottery_result_by_date();
		$this->CI->clisttemplate->set_ajax_action(array(
			array('field'=>'view','ftype'=>'dialog','fparam'=>array('cname','cdate')),
			array('field'=>'sms_send','fcompare'=>'sms_autosend','ftype'=>'link','fparam'=>array('cname','cdate')),
			array('field'=>'delete','ftype'=>'dialog','fparam'=>array('cname','cdate'))
		));
		$this->CI->clisttemplate->set_base_url(array(
			'view'=>'/admin/page/lottery/ajax_lottery_result_view/',
			'sms_send'=>'/admin/page/lottery_sms/sms_bulk_send/',
			'delete'=>'/admin/page/lottery/ajax_lottery_result_delete/'
		));		
		$list = $this->CI->clisttemplate->get_list($data,"default_list_xdelete");
		$this->CI->load->admin_view('default_list',array("list"=>$list));
	}
	
	function lottery_add_menu(){		
		$this->CI->load->library('clisttemplate');		
		$this->CI->cpage->set_page_title('New Lottery Result Add Menu');		
		$data['data'] = $this->CI->mlotteryresource->web_get_mresult_list();
		$data['header_allow'] = array('ccountry','cdesc','cname','cauto','cstatus');		
		$this->CI->clisttemplate->set_ajax_action(array(array('field'=>'add','fparam'=>array('cname'),'ftype'=>'link')));
		$this->CI->clisttemplate->set_base_url(array('add'=>'/admin/page/lottery/lottery_add_by_type'));		
		$list = $this->CI->clisttemplate->get_list($data,"default_list_xdelete");		
		$this->CI->load->admin_view('default_list',array("list"=>$list));
	}
	
	function lottery_add_by_type($type){
		$this->CI->load->library('cmessage');		
		$this->CI->load->library('clotteryresource');
		$data = $this->CI->input->get_form_data();
		if(strlen($data['lottery_type'])>0){
			$type = $data['lottery_type'];
		}
		$mresult = $this->CI->mlotteryresource->get_mresult_list_by_name($type);
		if(!$mresult){
			$this->CI->cmessage->set_response_message("Invalid lottery type!","error","/admin/page/lottery/lottery_add_menu");
		}
		$data['lottery_type'] = $type;
		if(ISSET($data['section']) && $data['section']=="add"){
			$result = $this->CI->clotteryresource->lottery_save_result($data);
			if($result){
				$this->CI->cmessage->set_response_message("Lottery result - ".$mresult[0]['cdesc']." add successfully!","notice","/admin/page/lottery/lottery_add_menu");
			}else{
				$this->CI->cmessage->set_response_message("Fail to add lottery result!","error");
			}
		}
		$this->CI->cpage->set_page_title('New Lottery Result - '.$mresult[0]['cdesc']);
		$this->CI->cpage->set_sub_menu(array("back"=>"/admin/page/lottery/lottery_add_menu/"));		
		$types = $this->CI->mlotteryresource->get_lottery_title_by_name($mresult[0]['cname']);
		$this->CI->load->admin_view('default_lottery_add_form',array("data"=>$data,"types"=>$types));
	}
	
	
/*
 * AJAX FUNCTION
 */
	function ajax_edit($table,$id){
		$this->CI->output->set_ajax();
		
		$data = $this->CI->mlotteryresource->get_lottery_result_by_table_id($table,$id);
		if($data){			
			$type = $this->CI->mlotteryresource->get_mresult_table_by_param(array("cfrom"=>$data[0]['cfrom'],"ctitle"=>$data[0]['ctitle'],"ctable"=>$table));
			echo $this->CI->load->admin_view('ajax_lottery_edit',array("data"=>$data[0],"regexp"=>$type[0]['cdigit_regexp']),TRUE);
			return true;
		}		
		$this->CI->load->library('cmessage');
		$this->CI->cmessage->set_response_message("error");
		echo '<script>JFunc.Popup.close(jQuery("$$owner_id"));</script>';
	}
	
	function ajax_confirm_edit(){		
		$this->CI->output->set_ajax();
		$this->CI->load->library('clotteryresource');
		$data = $_GET;
		$result = $this->CI->clotteryresource->lottery_update_result_one($data);
		if($result){
			echo '<script>window.location.reload();</script>';
			echo '<script>JFunc.Popup.close(jQuery("$$owner_id"));</script>';
			return true;
		}		
		$this->CI->load->library('cmessage');
		$this->CI->cmessage->set_response_message("error");
		echo '<script>JFunc.Popup.close(jQuery("$$owner_id"));</script>';
	}
	
	function ajax_lottery_delete($id){
		$this->CI->output->set_ajax();
		echo'<script>if(confirm("Are you confirm to delete?")){JFunc.Jax.dialog("ajax_lottery_delete_confirm/'.$id.'","$$owner");}else{JFunc.Popup.close(jQuery("$$owner_id"));}</script>';		
	}
	
	function ajax_lottery_delete_confirm($id){
		$this->CI->output->set_ajax();		
		$result = $this->CI->mlotteryresource->get_history_by_id($id);
		if($result){
			$this->CI->mlotteryresource->history_delete($id);
			$data = $this->CI->mlotteryresource->result_delete($result[0]['cfrom'],$result[0]['cdate'],$result[0]['ctitle']);
			if($data){				
				echo '<script>window.location.reload();</script>';
				//echo '<script>JFunc.Popup.close(jQuery("$$owner_id"));</script>';
				return true;
			}
		}
		$this->CI->load->library('cmessage');
		$this->CI->cmessage->set_response_message("error");
		echo '<script>JFunc.Popup.close(jQuery("$$owner_id"));</script>';
	}
	
	function ajax_lottery_status($id){
		$this->CI->output->set_ajax();		
        $result = $this->CI->mlotteryresource->get_history_by_id($id);
		if($result[0]['cstatus']==0){
			$cstatus = 1;
		}else{
			$cstatus = 0;
		}
		$this->CI->mlotteryresource->history_update_status($id,$cstatus);
		$data = $this->CI->mlotteryresource->get_history_by_id($id);
		if($data){
			echo'<script>jQuery("#cstatus_'.$id.'").html(unescape(\''. urlencode($data[0]['cstatus']) .'\'))</script>';			
		}
	}
	
	function ajax_lottery_waiting_list_process($id){
		$this->CI->output->set_ajax();		
		$this->CI->load->library('clottery_waiting_list');
        $result = $this->CI->mlottery_waiting_list->get_waiting_list_by_id($id);
		if( array_search($result[0]['ctype'], array('1','2','3'))!==FALSE && array_search($result[0]['cstatus'], array('0','2','8'))!==FALSE ){
			$this->CI->mlottery_waiting_list->waiting_list_update_status($id,'5');
			echo '<script>window.location.reload();</script>';
			echo '<script>JFunc.Popup.close(jQuery("$$owner_id"));</script>';
			return true;
		}else{
			echo '<script>alert("Fail to process!");</script>';
		}
	}
	
	function ajax_lottery_result_view($type,$date){
		$this->CI->output->set_ajax();		
		$this->CI->load->library('clotteryresource');
        $text = $this->CI->clotteryresource->sms_result_contents($type,$date);		
		if(strlen($text)>0){
			$text = preg_replace("#[\x0a]#i", "<br>", $text);
			echo '
				<div style="padding:5px;text-align:left;width:100%;height:100%;">
				'.$text.'
				</div>
				';			
		}else{
			echo'No result found!';
		}		
	}
	
	function ajax_lottery_result_delete($type,$date){
		$this->CI->output->set_ajax();
		echo'<script>if(confirm("Are you confirm to delete?")){JFunc.Jax.dialog("ajax_lottery_result_delete_confirm/'.$type.'/'.$date.'","$$owner");}else{JFunc.Popup.close(jQuery("$$owner_id"));}</script>';		
	}
	
	function ajax_lottery_result_delete_confirm($type,$date){
		$this->CI->output->set_ajax();	
		$types = $this->CI->mlotteryresource->get_lottery_title_by_name($type);
		$error = 0;
		if($types){
			foreach($types as $type){
				$result = $this->CI->mlotteryresource->get_history($date,$type['cfrom'],$type['ctitle']);
				if($result){
					$this->CI->mlotteryresource->history_delete($result[0]['id']);
					$data = $this->CI->mlotteryresource->result_delete($result[0]['cfrom'],$result[0]['cdate'],$result[0]['ctitle']);
					if(!$data){
						$error += 1;
					}
				}
			}
			if(!$error){
				echo '<script>window.location.reload();</script>';
				//echo '<script>JFunc.Popup.close(jQuery("$$owner_id"));</script>';
				return true;
			}
		}		
		$this->CI->load->library('cmessage');
		$this->CI->cmessage->set_response_message("error");
		echo '<script>JFunc.Popup.close(jQuery("$$owner_id"));</script>';
	}
    
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminControllerTran {
	
    function __construct() {
		$this->CI =& get_instance();		
		$this->CI->load->model('mtran');
	}	
	
	function index(){		
		$this->tran_order_list_view();
	}
	
	function tran_order_list_view(){
		$data = $this->CI->input->get_form_data();
		if(ISSET($data['section']) && $data['section']=="delete"){
			if(ISSET($data) && sizeof($data)>0){
				$data_arr = array();
				foreach($data as $key => $value){
					if(strtolower(substr($key,0,4))=="chk_"){
						$temp = substr($key,4,strlen($key));
						$data_arr[] = $temp;
					}
				}
				$result = $this->CI->mtran->delete_multiple_tran_order($data_arr);
				if($result){
					$this->CI->load->library('cmessage');
					$this->CI->cmessage->set_response_message("Order transaction deleted successfully","notice",$_SERVER['REQUEST_URI']);				
				}
			}
		}
		$this->CI->load->library('clisttemplate');		
		$this->CI->cpage->set_page_title('Order Transaction List');
		$this->CI->cpage->set_sub_menu(array("add"=>"/admin/page/tran/add_tran_order"));
		
		$data['data'] = $this->CI->mtran->get_tran_order_list();
		$list = $this->CI->clisttemplate->get_list($data,"default_list_xdelete");
		
		$this->CI->load->admin_view('default_list',array("list"=>$list));
	}
	
	function tran_order_refundable_list_view(){
		$data = $this->CI->input->get_form_data();		
		$this->CI->load->library('clisttemplate');		
		$this->CI->cpage->set_page_title('Order Transaction Refundable List');		
				
		$this->CI->clisttemplate->set_ajax_action(array('refund'=>'dialog'));
		$this->CI->clisttemplate->set_base_url(array('refund'=>'ajax_tran_order_refund'));
		$data['data'] = $this->CI->mtran->get_tran_order_refundable_list();
		$list = $this->CI->clisttemplate->get_list($data,"default_list_xdelete");
		
		$this->CI->load->admin_view('default_list',array("list"=>$list));
	}
	
	function add_tran_order(){				
		$this->CI->cpage->set_page_title('Add New Order');
		$data = $this->CI->input->get_form_data();
		if(ISSET($data['section']) && $data['section']=="add"){
			$this->CI->load->library('cuser');
			$this->CI->load->library('cmessage');
			$user = $this->CI->muser->get_user_by_username($data['username']);
			if($user){
				if($data['qty']>0){
					$p = $this->CI->mtran->get_tran_product_code_by_code($data['form_group']);
					if($p){
						$cpoints = $p[0]['cuqty'] * $data['qty'];
						$total = $p[0]['cuprice'] * $data['qty'];
						$tran = $this->CI->mtran->add_tran_order($user[0]['id'],$p[0]['ccode'],$data['qty'],$total,$cpoints,$data['date'],$data['comments'],1);
						if($tran){
							$this->CI->muser_points->points_add($cpoints,"[Tran Code: ".$tran[0]['ctran_no']."]. Subscription for ".$data['phone']." - ".$data['qty']." X [".$p[0]['cdesc']."]",$user[0]['id']);
							$this->CI->cmessage->set_response_message("New order ".$tran[0]['ctran_no']." added successfully","notice",'/admin/page/tran/tran_order_list_view');
						}else{
							$this->CI->cmessage->set_response_message("Add new order fail!","error");
						}
					}else{
						$this->CI->cmessage->set_response_message("Product code not found!","error");
					}
				}else{
					$this->cmessage->set_response_message("Invalid quantity!","error");
				}
			}else{
				$this->CI->cmessage->set_response_message("Username not found!","error");
			}
		}	
		$this->CI->cpage->set_sub_menu(array("back"=>"/admin/page/tran/tran_order_list_view"));
		$group = $this->CI->mtran->get_tran_product_code_list();
		$data['group'] = $group;
		if(!isset($data['date'])){
			$data['date'] = date("Y-m-d H:i:s");
		}		
		$this->CI->load->admin_view('tran_order_add',array("data"=>$data));
	}
	
	function tran_product_code_management_list_view(){
		$data = $this->CI->input->get_form_data();
		if(ISSET($data['section']) && $data['section']=="delete"){
			if(ISSET($data) && sizeof($data)>0){
				$data_arr = array();
				foreach($data as $key => $value){
					if(strtolower(substr($key,0,4))=="chk_"){
						$temp = substr($key,4,strlen($key));
						$data_arr[] = $temp;
					}
				}
				$result = $this->CI->mtran->delete_multiple_tran_product_code($data_arr);
				if($result){
					$this->CI->load->library('cmessage');
					$this->CI->cmessage->set_response_message("Product code deleted successfully","notice",$_SERVER['REQUEST_URI']);				
				}
			}
		}
		$this->CI->load->library('clisttemplate');		
		$this->CI->cpage->set_page_title('Product Code Management');		
		$this->CI->cpage->set_sub_menu(array("add"=>"/admin/page/tran/add_tran_product_code"));		
				
		$data['data'] = $this->CI->mtran->get_tran_product_code_list();
		$list = $this->CI->clisttemplate->get_list($data);
		
		$this->CI->load->admin_view('default_list',array("list"=>$list));
	}
	
	function add_tran_product_code(){				
		$this->CI->cpage->set_page_title('Add New Product Code');
		$data = $this->CI->input->get_form_data();
		if(ISSET($data['section']) && $data['section']=="add"){
			$error=0;			
			$this->CI->load->library('cmessage');
			if($data['code']==""){
				$error += 1;
				$this->CI->cmessage->set_response_message("Product code empty!","error");
			}else if($data['desc']==""){
				$error += 1;
				$this->CI->cmessage->set_response_message("Product description empty!","error");
			}
			$temp = $this->CI->mtran->get_tran_product_code_by_code($data['code']);
			if($temp){
				$error += 1;
				$this->CI->cmessage->set_response_message("Product code exists!","error");
			}
			if($error==0){
				if($this->CI->mtran->add_tran_product_code($data['code'],$data['desc'],$data['qty'],$data['price'])){
					$this->CI->cmessage->set_response_message("Product code added successfully","notice",'/admin/page/tran/tran_product_code_management_list_view');
				}else{
					$this->CI->cmessage->set_response_message("Product code fail to add!","error");
				}
			}			
		}
		$this->CI->cpage->set_sub_menu(array("back"=>"/admin/page/tran/tran_product_code_management_list_view/"));		
		$this->CI->load->admin_view('tran_product_code_add',array("data"=>$data));
	}
	
	function ajax_tran_order_refund($id){
		$this->CI->output->set_ajax();
		echo'<script>if(confirm("Are you confirm to refund transaction with ID: '.$id.'?")){JFunc.Jax.dialog("ajax_tran_order_refund_confirm/'.$id.'","$$owner");}else{JFunc.Popup.close(jQuery("$$owner_id"));}</script>';		
	}
	
	function ajax_tran_order_refund_confirm($id){
		$this->CI->load->library('cmessage');
		$this->CI->output->set_ajax();		
		$data = $this->CI->mtran->get_tran_order_by_id($id);
		if($data){
			$this->CI->load->library('cuser');
			$user = $this->CI->muser->get_user($data[0]['ccid']);
			if($user){
				if($data[0]['cqty']>0){
					$p = $this->CI->mtran->get_tran_product_code_by_code($data[0]['ccode']);
					if($p){
						$cpoints = $p[0]['cuqty'] * $data[0]['cqty'];
						$total = $p[0]['cuprice'] * $data[0]['cqty'];
						$tran = $this->CI->mtran->tran_order_refund($data[0]['id']);
						if($tran){
							$this->CI->muser_points->points_add(($cpoints*-1),"[Tran Code: ".$data[0]['ctran_no']."] Refund",$user[0]['id']);
							$this->CI->cmessage->set_response_message("order ".$data[0]['ctran_no']." refund successfully","notice");
							echo '<script>window.location.reload();</script>';
							//echo '<script>JFunc.Popup.close(jQuery("$$owner_id"));</script>';
							return true;
						}else{
							$this->CI->cmessage->set_response_message("Add new order fail!","error");
						}
					}else{
						$this->CI->cmessage->set_response_message("Product code not found!","error");
					}
				}else{
					$this->cmessage->set_response_message("Invalid quantity!","error");
				}
			}else{
				$this->CI->cmessage->set_response_message("Username not found!","error");
			}
		}
		
		$this->CI->cmessage->set_response_message("error");
		echo '<script>JFunc.Popup.close(jQuery("$$owner_id"));</script>';
	}
    
}
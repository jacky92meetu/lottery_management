<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

ini_set('mysql.connect_timeout', 30); 
set_time_limit(60);

class Cronjob extends CI_Controller {

	function __construct() {
		parent::__construct();		
		$this->cpage->set_template('no_frame');
		$this->load->library('ccfg');
		$this->load->library('clottery');
		$this->load->library('clottery_ajax');
	}
	
	function index()
	{
		$this->result_4d2u_update();		
	}
	
	function result_sms_processlist(){
		$this->cpage->set_page_title('CRONJOB - PROCESS SMS & SEND');
		$this->load->view("clottery_resource/sms_cronjob_sms_processlist");
	}
	
	function result_sms_check(){
		$this->cpage->set_page_title('CRONJOB - RESULT CHECK & SMS SEND');
		$this->load->view("clottery_resource/sms_cronjob_check_send");
	}
	
	function mnc_check(){
		$this->cpage->set_page_title('CRONJOB - MNC CHECK');
		$this->load->view("clottery_resource/sms_cronjob_mnc");
	}
	
	function waiting_list_process(){
		$this->cpage->set_page_title('CRONJOB - WAITING LIST PROCESS');
		$this->load->view("clottery_resource/sms_cronjob_waiting_list");
	}
	
	function result_4d2u_update(){
		$this->cpage->set_page_title('CRONJOB - 4D AUTO UPDATE FROM 4D2U.COM');
		$this->load->view("clottery_resource/sms_cronjob_4d2u_update");
	}
	
	function result_manual_4d2u_update(){
		$this->cpage->set_page_title('CRONJOB - 4D MANUAL UPDATE FROM 4D2U.COM');
		$this->load->view("clottery_resource/sms_manual_4d2u_update");
	} 
	
	function result_4d88_update(){
		$this->cpage->set_page_title('CRONJOB - RESULT AUTO UPDATE FROM 4D88.COM');
		$this->load->view("clottery_resource/sms_cronjob_4d88_update_v2");
	}
	
	function result_manual_4d88_update(){
		$this->cpage->set_page_title('CRONJOB - 4D MANUAL UPDATE FROM 4D88.COM');
		$this->load->view("clottery_resource/sms_manual_4d88_update");
	}
	
	
/*
 * Ajax Function
 */		
	function ajax_result_get(){		
		$this->output->set_ajax();
		if(!isset($_GET['resource'])){
			exit;
		}
		$from = $_GET['resource'];
		$this->load->library('ccfg');
		if($from=="4d2u"){
			$this->ccfg->sys_set_timeout('4d2u_result_update_status',10);		
			$this->load->library('clottery_resource/clottery4d2u');
			print $this->clottery4d2u->endata();					
		}else if($from=="4d2u_manual"){
			$this->load->library('clottery_resource/clottery4d2u');
			print $this->clottery4d2u->manual_update();		
		}else if($from=="4d88"){
			$this->ccfg->sys_set_timeout('4d88_result_update_status',10);
			$this->load->library('clottery_resource/clottery4d88v2');		
			print $this->clottery4d88v2->endata();		
		}else if($from=="4d88_manual"){
			$this->load->library('clottery_resource/clottery4d88v2');
			print $this->clottery4d88v2->manual_update();		
		}
		exit;
	}
	
	function ajax_result_update(){
		$this->output->set_ajax();
		if(!isset($_GET['resource'])){
			exit;
		}
		$from = $_GET['resource'];
		$this->load->library('ccfg');
		if($from=="4d2u"){
			$this->ccfg->sys_set_timeout('4d2u_result_update_status',10);		
			$this->load->library('clottery_resource/clottery4d2u');
			print $this->clottery4d2u->save_endata();		
		}else if($from=="4d88"){
			$this->ccfg->sys_set_timeout('4d88_result_update_status',10);
			$this->load->library('clottery_resource/clottery4d88v2');		
			print $this->clottery4d88v2->save_endata();		
		}
		exit;		
	}	
	
	function ajax_sms_cs(){
		$this->output->set_ajax();
		$this->load->library('ccfg');
		$this->ccfg->sys_set_timeout('result_sms_check_status',10);
		print json_encode($this->clottery_ajax->ajax_check_send());
		exit;
	}
	
	function ajax_sms_mnc(){
		$this->output->set_ajax();
		$this->load->library('ccfg');		
		$this->ccfg->sys_set_timeout('result_sms_mnc_status',10);		
		print json_encode($this->clottery_ajax->ajax_mnc_check());		
		exit;
	}
	
	function ajax_waiting_list_process(){
		$this->output->set_ajax();
		$this->load->library('ccfg');		
		$this->ccfg->sys_set_timeout('result_waiting_list_process_status',10);
		print json_encode($this->clottery_ajax->ajax_waiting_list_process());		
		exit;
	}
	
	function ajax_sms_processlist(){
		$this->output->set_ajax();
//		$this->load->library('ccfg');		
//		$this->ccfg->sys_set_timeout('sms_processlist_run',10);
		print json_encode($this->clottery_ajax->ajax_sms_processlist());		
		exit;
	}
	
}
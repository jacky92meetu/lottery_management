<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>


<div style="padding:10px;width:500px;">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>		
			<td class="control_panel" align="left" valign="top" style="padding:5px;">
				<?php
					if(ISSET($link) && $link!==false){				
						foreach($link as $key => $url){
							$img = $this->CI->lang->image('small_'.$key);
							echo'
								<div style="padding:5px;float:left;">
									<a desc="'.$this->CI->lang->line('desc_'.$key).'" href="'.$url.'">
										<div style="background-image: url('.$img.')">
											'.$this->CI->lang->line('desc_'.$key).'
										</div>
									</a>
								</div>						
							';
						}
					}
				?>			
			</td>				
		</tr>
	</table>	
</div>
 
<div style="border-top:1px #CCCCCC solid;height:1px;width:100%;"></div>

<script>
	var $myCal;
	jQuery(function(){
		$myCal = new myCal;		
	});
	var myCal = function(){	
		this.img = {
			0:"/application/globals/images/system/ico/ico_block_0.jpg",
			1:"/application/globals/images/system/ico/ico_block_1.jpg",
			2:"/application/globals/images/system/ico/ico_warning.jpg"
		}
		this.count = 0;
		this.error = 0;
		this.data = null;		
		this.is_running = 0;		
		this.selected_batch = "";
		this.xtime1 = null;
		this.start();
	}
	myCal.prototype = {
		start:function(){
			var $self;
			$self = this;			
			jQuery('.img_ico').attr('src',$self.img[0]);
			$self.xtime1 = setInterval(function(){$self.run();},100);			
		},
		stop:function($id){
			var $self;
			$self = this;
			if($id==undefined){
				return;
			}
			$id = 'xtime'+$id;
			if($self[$id]){
				clearInterval($self[$id]);
				$self[$id] = null;
			}
		},		
		run:function(){
			var $self;
			$self = this;
			if($self.is_running==0){				
				$self.is_running = 4;				
				jQuery('#timing').html((new Date().toString()));
				$self.get_data();
			}else if($self.is_running==6){				
				if($self.count==0){
					$self.is_running = 0;
				}				
			}			
		},		
		get_data:function(){
			var $self;
			$self = this;
			$self.count = 0;
			if($self.is_running==4){
				$self.is_running = 5;
				
				//dashboard data
				$self.count += 1;
				jQuery.get('/admin/ajax_check_status',{},function($data){
					try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}										
					if($data.status==1){
						for(var $a in $data.data){
							if($data.data[$a]=='0'){
								jQuery('#img_'+$a).attr('src',$self.img[1]);
							}else if($data.data[$a]=='1'){
								jQuery('#img_'+$a).attr('src',$self.img[0]);
							}else{
								jQuery('#img_'+$a).attr('src',$self.img[$data.data[$a]]);
							}							
						}
					}
				}).complete(function(){ $self.count -= 1; });
				
				//sms result send data
				$self.count += 1;
				jQuery.get('/admin/ajax_check_send_status',{},function($data){
					try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}										
					if($data.status==1){
						for(var $a in $data.data){
							jQuery('#img_'+$a).attr('src',$self.img[$data.data[$a]]);
							if($data.data[$a]=='1'){
								jQuery('#img_'+$a).closest('tr').find('.button a').show();
							}else{
								jQuery('#img_'+$a).closest('tr').find('.button a').hide();
							}
						}
					}
				}).complete(function(){ $self.count -= 1; });
				$self.is_running = 6;
			}
		}		
	}
</script>

<div id="div_sms_send" style="float:left;padding:10px;border:1px #CCCCCC solid;margin:5px;">	
	<table class="std_form1" cellspacing="0" cellpadding="0" border="0">		
		<tr>
			<td colspan="3">
				<div class="title">TODAY SMS SEND PENDING</div>
			</td>
		</tr>
		<?php
			foreach($lottery_type as $type){
				echo'
					<tr>
						<td align="right" class="label">'.$type['cdesc'].'</td>					
						<td align="center" class="data"><img id="img_'.$type['cname'].'" src="" class="img_ico"></td>
						<td class="button"><a href="/admin/page/lottery_sms/sms_bulk_send/'.$type['cname'].'/'.date('Y-m-d').'" target="_self" style="display:none;">SEND NOW</a></td>
					</tr>
				';
			}
		?>
	</table>	
</div>

<div style="float:left;padding:10px;border:1px #CCCCCC solid;margin:5px;">	
	<table class="std_form1" cellspacing="0" cellpadding="0" border="0">		
		<tr>
			<td colspan="3">
				<div class="title">CRONJOB LOTTERY RESULT</div>
			</td>
		</tr>		
		<tr>
			<td align="right" class="label">4D AUTO UPDATE FROM <strong>4D2U.COM</strong></td>					
			<td align="center" class="data"><img id="img_4d2u_result_update_status" src="" class="img_ico"></td>			
			<!--<td class="button"><a href="/cronjob/result_4d2u_update" target="_blank">OPEN</a></td>-->
		</tr>		
		<tr>
			<td align="right" class="label">4D AUTO UPDATE FROM <strong>4D88.COM</strong></td>					
			<td align="center" class="data"><img id="img_4d88_result_update_status" src="" class="img_ico"></td>			
			<!--<td class="button"><a href="/cronjob/result_4d88_update" target="_blank">OPEN</a></td>-->
		</tr>		
	</table>	
</div>	

<div style="float:left;padding:10px;border:1px #CCCCCC solid;margin:5px;">	
	<table class="std_form1" cellspacing="0" cellpadding="0" border="0">		
		<tr>
			<td colspan="3">
				<div class="title">CRONJOB TASK LIST</div>
			</td>
		</tr>
		<tr>
			<td align="right" class="label">PROCESS SMS & SEND</td>					
			<td align="center" class="data"><img id="img_sms_processlist_run" src="" class="img_ico"></td>			
			<!--<td class="button"><a href="/cronjob/result_sms_processlist" target="_blank">OPEN</a></td>-->
		</tr>	
		<tr>
			<td align="right" class="label">RESULT CHECK & SMS SEND</td>					
			<td align="center" class="data"><img id="img_result_sms_check_status" src="" class="img_ico"></td>			
			<!--<td class="button"><a href="/cronjob/result_sms_check" target="_blank">OPEN</a></td>-->
		</tr>	
		<tr>
			<td align="right" class="label">MNC CHECK</td>					
			<td align="center" class="data"><img id="img_result_sms_mnc_status" src="" class="img_ico"></td>			
			<!--<td class="button"><a href="/cronjob/mnc_check" target="_blank">OPEN</a></td>-->
		</tr>			
		<tr>
			<td align="right" class="label">WAITING LIST PROCESS</td>					
			<td align="center" class="data"><img id="img_result_waiting_list_process_status" src="" class="img_ico"></td>			
			<!--<td class="button"><a href="/cronjob/waiting_list_process" target="_blank">OPEN</a></td>-->
		</tr>			
	</table>
</div>
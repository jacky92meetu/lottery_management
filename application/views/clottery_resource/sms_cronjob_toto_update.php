<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php 
	header('Content-Type: text/html; charset=UTF-8');
?>

<script>
	var $myCal;
	jQuery(function(){
		$myCal = new myCal;		
	});
	var myCal = function(){
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
			$self.xtime1 = setInterval(function(){$self.run();},1000);			
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
				$self.is_running = 1;
//				jQuery('#timing').html((new Date().toString()));
//				var $start_time = "<?php echo strtotime(date("Y-m-d")." ".$this->ccfg->get('result_toto_update_time_start'))*1000; ?>";
//				var $end_time = "<?php echo strtotime(date("Y-m-d")." ".$this->ccfg->get('result_toto_update_time_end'))*1000; ?>";
//				var $cur_time = (new Date()).getTime();				
//				if($cur_time>=$start_time && $cur_time<=$end_time){
//					$self.is_running = 3;
//				}else{
//					$self.is_running = 0;
//				}	
				jQuery('#timing').html((new Date().toString()));
				$self.is_running = 3;
			}else if($self.is_running==3){
				$self.is_running = 4;				
				$self.get_data();
			}else if($self.is_running==6){				
				if($self.count==0){
					$self.is_running = 0;
				}				
			}			
		},		
		get_data:function(){
			var $self,$date,$temp,$container;
			$self = this;						
			$self.count = 0;
			if($self.is_running==4){
				$self.is_running = 5;				
				jQuery.get('/cronjob/ajax_result_get',{resource:'toto'},function($data){
					try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}					
					if($data.status==1){
						$temp = jQuery($data.data);
						$temp.find('img').attr('src','');
						$container = jQuery('#display').html($temp).find('td[width="394"][valign="top"][bgcolor="#FFFFFF"]');
						if($container.length){
							var $result = $container.text();
							$self.count += 1;
							jQuery.get('/cronjob/ajax_result_update',{data:encodeURI($result),resource:'toto'},function($data){
								try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}
								if($data.status==1){	
									jQuery('#result_container div:nth-child(n+100)').nextAll().remove();
									jQuery('<div style="margin:2px 0;padding:2px;border:1px solid #CCCCCC;"></div>').html($data.data).prependTo(jQuery('#result_container'));									
								}								
							}).complete(function() { $self.count -= 1; });
						}
					}
				}).complete(function() { $self.is_running = 6; });
			}
		}		
	}
</script>
<div>
	<img src="/application/globals/images/toto.jpg" style="height:50px;">
</div>
<div style="color: #ff0000;font: bold 32px arial;padding:20px 0;">PLEASE DO NOT CLOSE THIS BROWSER!!!</div>
<div id="timing" style="padding:10px 0;"></div>
<div id="results">		
	Result:
	<div id="result_container"></div>	
</div>   
<div id="display" style="display:none;"></div>
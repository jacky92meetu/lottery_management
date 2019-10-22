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
			var $self,$date,$container;
			$self = this;						
			$self.count = 0;
			if($self.is_running==4){
				$self.is_running = 5;
				$self.count += 1;
				jQuery.get('/cronjob/ajax_waiting_list_process',{},function($data){
					try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}										
					if($data.status==1){
						jQuery('#result_container div:nth-child(n+100)').nextAll().remove();
						jQuery('<div style="margin:2px 0;padding:2px;border:1px solid #CCCCCC;"></div>').html($data.data).prependTo(jQuery('#result_container'));						
					}
				}).complete(function() { $self.count -= 1; $self.is_running = 6; });
			}
		}		
	}
</script>

<div style="color: #ff0000;font: bold 32px arial;padding:20px 0;">PLEASE DO NOT CLOSE THIS BROWSER!!!</div>
<div id="timing" style="padding:10px 0;"></div>
<div id="results">	
	Result:
	<div id="result_container"></div>	
</div>   
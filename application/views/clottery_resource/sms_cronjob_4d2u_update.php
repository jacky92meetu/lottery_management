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
			var $self,$date,$temp,$container,$result;
			$self = this;						
			$self.count = 0;
			$result = 0;
			if($self.is_running==4){
				$self.is_running = 5;				
				jQuery.get('/cronjob/ajax_result_get',{act:'endata',batch:'all',resource:'4d2u'},function($data){
					try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}					
					if($data.status==1){
						$temp = jQuery($data.data);
						$temp.find('img').attr('src','');
						$container = jQuery('#display').html($temp);						
						$container.find('b img').each(function(){														
							if(jQuery(this).closest('tr').children().length==3){
								var $from = jQuery(this).attr('alt');
								var $date = $container.find('img[alt="'+$from+'"]').closest('tr').children('td:nth-child(2)').text();
								var $drawid = $container.find('img[alt="'+$from+'"]').closest('tr').children('td:nth-child(3)').text();
								$container.find('img[alt="'+$from+'"]').closest('table').nextAll().find('tbody').each(function(){
									var $table = jQuery(this);
									if($table.find('b img').length){
										return false;									
									}
									var $title = "";
									var $text = "";
									$table.find('tr[bgcolor:"FFCC66"]:nth-child(1)').each(function(){
										$title = jQuery(this).text();
									});
									$table.find('tr[bgcolor:"FFCC66"]:nth-child(1)').nextAll('tr[bgcolor:"bbbbbb"]').each(function(){
										$text += "\n"+jQuery(this).find('font[color="000077"]').map(function(){
													return jQuery(this).text();
												}).get().join("\n");
									});									
									$self.count += 1;
									jQuery.get('/cronjob/ajax_result_update',{act:'save_endata',batch:'all',from:encodeURI($from),date:encodeURI($date),drawid:encodeURI($drawid),title:encodeURI($title),data:encodeURI($text),resource:'4d2u'},function($data){										
										try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}										
										if($data.status==1){
											$result += 1;											
											jQuery('#result_container div:nth-child(n+100)').nextAll().remove();
											jQuery('<div style="margin:2px 0;padding:2px;border:1px solid #CCCCCC;"></div>').html($data.data).prependTo(jQuery('#result_container'));
										}										
									}).complete(function(){ $self.count -= 1; });
								});
							}							
						});
					}
				}).complete(function(){ $self.is_running = 6; });
			}
		}		
	}
</script>
<div>
	<img src="/application/globals/images/4d2u.jpg" style="height:50px;width:50px;">
</div>
<div style="color: #ff0000;font: bold 32px arial;padding:20px 0;">PLEASE DO NOT CLOSE THIS BROWSER!!!</div>
<div id="timing" style="padding:10px 0;"></div>
<div id="results">	
	LAST UPDATED DATE: <span id="current"></span><br>		
	Ajax Count: <span id="ajax"></span><br>
	Imported Count: <span id="count"></span><br>
	Error Count: <span id="error"></span><br>
</div>   
<div id="results">		
	Result:
	<div id="result_container"></div>	
</div>   
<div id="display" style="display:none;"></div>

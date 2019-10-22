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
		this.batch_from = "<?php echo $_GET['batch_from']; ?>";
		if(this.batch_from!=""){
			this.batch_from = new Date(this.batch_from);
		}
		this.batch_to = "<?php echo (isset($_GET['batch_to']))?$_GET['batch_to']:""; ?>";		
		if(this.batch_to!=""){
			this.batch_to = new Date(this.batch_to);
		}
		this.count = 0;
		this.error = 0;
		this.data = null;		
		this.is_running = 0;		
		this.selected_batch = this.batch_from;
		this.xtime1 = null;		
		this.start();
	}
	myCal.prototype = {
		start:function(){
			var $self;
			$self = this;
			if($self.batch_to==""){
				$self.batch_to = $self.batch_from;
			}
			jQuery('#batch_from').html($self.batch_from.toDateString());
			jQuery('#batch_to').html($self.batch_to.toDateString());
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
			var $self,$pct,$ndate;
			$self = this;
			if($self.is_running==0){
				$self.is_running = 1;				
				$self.get_data();				
			}else if($self.is_running==3){				
				if($self.count==0){
					$self.is_running = 5;
				}
			}else if($self.is_running==5){
				$self.is_running = 6;
				if(($self.batch_to.getTime()-$self.batch_from.getTime())<=0){
					$pct = '100%';
				}else{
					$pct = ((($self.selected_batch.getTime()-$self.batch_from.getTime())*1)/($self.batch_to.getTime()-$self.batch_from.getTime())*100).toFixed(2)+'%';
				}
				jQuery('#pct').html($pct);
				jQuery('title').html($pct);
				$self.is_running = 8;
			}else if($self.is_running==8){			
				$self.is_running = 9;
				$self.selected_batch = new Date($self.selected_batch.getTime()+(24*60*60*1000));
				if($self.selected_batch.getTime()>$self.batch_to.getTime()){
					$self.stop(1);		
					return;
				}
				$self.is_running = 0;
			}			
		},		
		get_data:function(){
			var $self,$batch_no,$year,$month,$date,$container,$temp;
			$self = this;						
			$self.count = 0;
			if($self.is_running==1){
				$self.is_running = 2;
				$year = ("0000"+$self.selected_batch.getFullYear()).substr(-4);
				$month = ("00"+($self.selected_batch.getMonth()+1)).substr(-2);
				$date = ("00"+$self.selected_batch.getDate()).substr(-2);
				$batch_no = $year+"-"+$month+"-"+$date;
				jQuery('#current').html($batch_no);								
				jQuery.get('/cronjob/ajax_result_get',{act:'endata',batch_no:$batch_no,resource:'4d2u_manual'},function($data){
					try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}
					if($data.status==1){
						$temp = jQuery($data.data);
						$temp.find('img').attr('src','');
						$container = jQuery('#display').html($temp);
						$container.find('b img').each(function(){
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
								jQuery.get('/cronjob/ajax_result_update',{act:'save_endata',from:encodeURI($from),date:encodeURI($date),drawid:encodeURI($drawid),title:encodeURI($title),data:encodeURI($text),resource:'4d2u'},function($data){
									try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}									
									if($data.status==1){										
										jQuery('#result_container div:nth-child(n+100)').nextAll().remove();
										jQuery('<div style="margin:2px 0;padding:2px;border:1px solid #CCCCCC;"></div>').html($data.data).prependTo(jQuery('#result_container'));
									}									
								}).complete(function() { $self.count -= 1; });
							});
						});
					}else{
						$self.error += 1;
						jQuery('#error').html(jQuery('#error').html()+','+$batch_no);						
					}
				}).complete(function() { $self.is_running = 3; });
			}
		}		
	}
</script>

<div>
	<img src="/application/globals/images/4d2u.jpg" style="height:50px;">
</div>
<div>START IMPORT</div>
<div id="results">
	Length: <span id="batch_from"></span> - <span id="batch_to"></span><br>
	Percentage: <span id="pct"></span><br>
	Current: <span id="current"></span><br>
	Imported Count: <span id="count"></span><br>
	Error Count: <span id="error"></span>
</div>
<div id="results">		
	Result:
	<div id="result_container"></div>	
</div>   
<div id="display" style="display:none;"></div>
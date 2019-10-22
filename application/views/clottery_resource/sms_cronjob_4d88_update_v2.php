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
					$self.is_running = 8;					
				}
			}else if($self.is_running==8){
				$self.is_running = 9;
				setTimeout(function(){$self.is_running = 0;},60000);
			}			
		},		
		get_data:function(){
			var $self,$date,$temp,$container;
			$self = this;
			if($self.is_running==4){
				$self.is_running = 5;
				jQuery.get('/cronjob/ajax_result_get',{resource:'4d88'},function($data){
					try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}					
					if($data.status==1){	
						$temp = jQuery($data.data);
						$temp.find('img').attr('src','');
						$container = jQuery('#display').html($temp);
						if($container.length){
							
							//damacai 4d update
							$self.count += 1;
							var $table = $container.find('font:contains(DAMACAI 1+3D)').closest('table.curve');
							var $date = $table.find('strong font').eq(1).text();
							var $draw = $table.find('strong font').eq(2).text();
							var $4d = $table.children('tbody').children('tr').eq(1).find('div').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==4){return $temp;}}).get().join("||");
							var $special = $table.find('strong:contains(Starters)').closest('tbody').children('tr:gt(0)').find('td').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==4){return $temp;}}).get().join("||");
							var $consolation = $table.find('strong:contains(Consolation)').closest('tbody').children('tr:gt(0)').find('td').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==4){return $temp;}}).get().join("||");							
							jQuery.get('/cronjob/ajax_result_update',{from:"damacai",title:"4d",draw:encodeURI($draw),date:encodeURI($date),r_4d:encodeURI($4d),r_special:encodeURI($special),r_consolation:encodeURI($consolation),resource:'4d88'},function($data){
								try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}
								if($data.status==1){	
									jQuery('#result_container div:nth-child(n+100)').nextAll().remove();
									jQuery('<div style="margin:2px 0;padding:2px;border:1px solid #CCCCCC;"></div>').html($data.data).prependTo(jQuery('#result_container'));									
								}								
							}).complete(function(){ $self.count -= 1; });
							
							//toto 4d update
							$self.count += 1;
							var $table = $container.find('font:contains(TOTO 4D)').closest('table.curve');
							var $date = $table.find('strong font').eq(1).text();
							var $draw = $table.find('strong font').eq(2).text();							
							var $4d = $table.children('tbody').children('tr').eq(1).find('div').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==4){return $temp;}}).get().join("||");
							var $special = $table.find('strong:contains(Special)').closest('tbody').children('tr:gt(0)').find('td').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==4){return $temp;}}).get().join("||");
							var $consolation = $table.find('strong:contains(Consolation)').closest('tbody').children('tr:gt(0)').find('td').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==4){return $temp;}}).get().join("||");
							jQuery.get('/cronjob/ajax_result_update',{from:"toto",title:"4d",draw:encodeURI($draw),date:encodeURI($date),r_4d:encodeURI($4d),r_special:encodeURI($special),r_consolation:encodeURI($consolation),resource:'4d88'},function($data){
								try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}
								if($data.status==1){	
									jQuery('#result_container div:nth-child(n+100)').nextAll().remove();
									jQuery('<div style="margin:2px 0;padding:2px;border:1px solid #CCCCCC;"></div>').html($data.data).prependTo(jQuery('#result_container'));									
								}								
							}).complete(function(){ $self.count -= 1; });
							
							//toto 5d update
							$self.count += 1;
							var $table = $container.find('font:contains(TOTO 5D)').closest('table.curve').children('tbody').children('tr');
							var $date = $table.find('strong font').eq(1).text();
							var $draw = $table.find('strong font').eq(2).text();							
							var $5d = $table.eq(1).find('td.linesbox').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==5){return $temp;}}).get().join("||");
							jQuery.get('/cronjob/ajax_result_update',{from:"toto",title:"5d",draw:encodeURI($draw),date:encodeURI($date),r_5d:encodeURI($5d),resource:'4d88'},function($data){
								try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}
								if($data.status==1){	
									jQuery('#result_container div:nth-child(n+100)').nextAll().remove();
									jQuery('<div style="margin:2px 0;padding:2px;border:1px solid #CCCCCC;"></div>').html($data.data).prependTo(jQuery('#result_container'));									
								}								
							}).complete(function(){ $self.count -= 1; });
							
							//toto 6d update
							$self.count += 1;
							var $table = $container.find('font:contains(TOTO 6D)').closest('table.curve').children('tbody').children('tr');
							var $date = $table.find('strong font').eq(1).text();
							var $draw = $table.find('strong font').eq(2).text();
							var $6d = $table.eq(1).find('td.d3rdtxt').text().replace(/\D/gi,"");
							jQuery.get('/cronjob/ajax_result_update',{from:"toto",title:"6d",draw:encodeURI($draw),date:encodeURI($date),r_6d:encodeURI($6d),resource:'4d88'},function($data){
								try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}
								if($data.status==1){	
									jQuery('#result_container div:nth-child(n+100)').nextAll().remove();
									jQuery('<div style="margin:2px 0;padding:2px;border:1px solid #CCCCCC;"></div>').html($data.data).prependTo(jQuery('#result_container'));									
								}								
							}).complete(function(){ $self.count -= 1; });
							
							//toto 658 update
							$self.count += 1;
							var $table = $container.find('font:contains(SUPREME TOTO)').closest('table.curve').children('tbody').children('tr');
							var $date = $table.find('strong font').eq(1).text();
							var $draw = $table.find('strong font').eq(2).text();
							var $658 = $table.eq(1).find('td.box').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length>=1 && $temp.length<=2){return $temp;}}).get().join(",");
							jQuery.get('/cronjob/ajax_result_update',{from:"toto",title:"658",draw:encodeURI($draw),date:encodeURI($date),r_658:encodeURI($658),resource:'4d88'},function($data){
								try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}
								if($data.status==1){	
									jQuery('#result_container div:nth-child(n+100)').nextAll().remove();
									jQuery('<div style="margin:2px 0;padding:2px;border:1px solid #CCCCCC;"></div>').html($data.data).prependTo(jQuery('#result_container'));									
								}								
							}).complete(function(){ $self.count -= 1; });
							
							//toto 655 update
							$self.count += 1;
							var $table = $container.find('font:contains(POWER TOTO)').closest('table.curve').children('tbody').children('tr');
							var $date = $table.find('strong font').eq(2).text();
							var $draw = $table.find('strong font').eq(3).text();
							var $655 = $table.eq(1).find('td.box').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length>=1 && $temp.length<=2){return $temp;}}).get().join(",");
							jQuery.get('/cronjob/ajax_result_update',{from:"toto",title:"655",draw:encodeURI($draw),date:encodeURI($date),r_655:encodeURI($655),resource:'4d88'},function($data){
								try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}
								if($data.status==1){	
									jQuery('#result_container div:nth-child(n+100)').nextAll().remove();
									jQuery('<div style="margin:2px 0;padding:2px;border:1px solid #CCCCCC;"></div>').html($data.data).prependTo(jQuery('#result_container'));									
								}								
							}).complete(function(){ $self.count -= 1; });
							
							//toto 652 update
							$self.count += 1;
							var $table = $container.find('font:contains(MEGA TOTO)').closest('table.curve').children('tbody').children('tr');
							var $date = $table.find('strong font').eq(1).text();
							var $draw = $table.find('strong font').eq(2).text();
							var $652 = $table.eq(1).find('td').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length>=1 && $temp.length<=2){return $temp;}}).get().join(",");
							jQuery.get('/cronjob/ajax_result_update',{from:"toto",title:"652",draw:encodeURI($draw),date:encodeURI($date),r_652:encodeURI($652),resource:'4d88'},function($data){
								try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}
								if($data.status==1){	
									jQuery('#result_container div:nth-child(n+100)').nextAll().remove();
									jQuery('<div style="margin:2px 0;padding:2px;border:1px solid #CCCCCC;"></div>').html($data.data).prependTo(jQuery('#result_container'));									
								}								
							}).complete(function(){ $self.count -= 1; });
							
							//magnum 4d update
							$self.count += 1;
							var $table = $container.find('font:contains(MAGNUM 4D)').closest('table.curve');
							var $date = $table.find('strong font').eq(1).text();
							var $draw = $table.find('strong font').eq(2).text();							
							var $4d = $table.children('tbody').children('tr').eq(1).find('div').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==4){return $temp;}}).get().join("||");
							var $special = $table.find('strong:contains(Special)').closest('tbody').children('tr:gt(0)').find('td').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==4){return $temp;}}).get().join("||");
							var $consolation = $table.find('strong:contains(Consolation)').closest('tbody').children('tr:gt(0)').find('td').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==4){return $temp;}}).get().join("||");							
							jQuery.get('/cronjob/ajax_result_update',{from:"magnum",title:"4d",draw:encodeURI($draw),date:encodeURI($date),r_4d:encodeURI($4d),r_special:encodeURI($special),r_consolation:encodeURI($consolation),resource:'4d88'},function($data){
								try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}
								if($data.status==1){	
									jQuery('#result_container div:nth-child(n+100)').nextAll().remove();
									jQuery('<div style="margin:2px 0;padding:2px;border:1px solid #CCCCCC;"></div>').html($data.data).prependTo(jQuery('#result_container'));									
								}								
							}).complete(function(){ $self.count -= 1; });
							
							//CASHSWEEP 1+3D update
							$self.count += 1;
							var $table = $container.find('font:contains(CASHSWEEP 1+3D)').closest('table.curve');
							var $date = $table.find('strong font').eq(1).text();
							var $draw = $table.find('strong font').eq(2).text();							
							var $4d = $table.children('tbody').children('tr').eq(1).find('div').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==4){return $temp;}}).get().join("||");
							var $special = $table.find('strong:contains(Special)').closest('tbody').children('tr:gt(0)').find('td').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==4){return $temp;}}).get().join("||");
							var $consolation = $table.find('strong:contains(Consolation)').closest('tbody').children('tr:gt(0)').find('td').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==4){return $temp;}}).get().join("||");
							jQuery.get('/cronjob/ajax_result_update',{from:"sweep",title:"4d",draw:encodeURI($draw),date:encodeURI($date),r_4d:encodeURI($4d),r_special:encodeURI($special),r_consolation:encodeURI($consolation),resource:'4d88'},function($data){
								try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}
								if($data.status==1){	
									jQuery('#result_container div:nth-child(n+100)').nextAll().remove();
									jQuery('<div style="margin:2px 0;padding:2px;border:1px solid #CCCCCC;"></div>').html($data.data).prependTo(jQuery('#result_container'));									
								}								
							}).complete(function(){ $self.count -= 1; });
							
							//SANDAKAN 4D update
							$self.count += 1;
							var $table = $container.find('font:contains(SANDAKAN 4D)').closest('table.curve');
							var $date = $table.find('strong font').eq(1).text();
							var $draw = $table.find('strong font').eq(2).text();							
							var $4d = $table.children('tbody').children('tr').eq(1).find('div').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==4){return $temp;}}).get().join("||");
							var $special = $table.find('strong:contains(Special)').closest('tbody').children('tr:gt(0)').find('td').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==4){return $temp;}}).get().join("||");
							var $consolation = $table.find('strong:contains(Consolation)').closest('tbody').children('tr:gt(0)').find('td').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==4){return $temp;}}).get().join("||");
							jQuery.get('/cronjob/ajax_result_update',{from:"sandakan",title:"4d",draw:encodeURI($draw),date:encodeURI($date),r_4d:encodeURI($4d),r_special:encodeURI($special),r_consolation:encodeURI($consolation),resource:'4d88'},function($data){
								try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}
								if($data.status==1){	
									jQuery('#result_container div:nth-child(n+100)').nextAll().remove();
									jQuery('<div style="margin:2px 0;padding:2px;border:1px solid #CCCCCC;"></div>').html($data.data).prependTo(jQuery('#result_container'));									
								}								
							}).complete(function(){ $self.count -= 1; });
							
							//SINGAPORE 4D update
							$self.count += 1;
							var $table = $container.find('font:contains(SINGAPORE 4D)').closest('table.curve');
							var $date = $table.find('strong font').eq(1).text();
							var $draw = $table.find('strong font').eq(2).text();							
							var $4d = $table.children('tbody').children('tr').eq(1).find('div').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==4){return $temp;}}).get().join("||");
							var $special = $table.find('strong:contains(Starters)').closest('tbody').children('tr:gt(0)').find('td').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==4){return $temp;}}).get().join("||");
							var $consolation = $table.find('strong:contains(Consolation)').closest('tbody').children('tr:gt(0)').find('td').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==4){return $temp;}}).get().join("||");
							jQuery.get('/cronjob/ajax_result_update',{from:"singapore",title:"4d",draw:encodeURI($draw),date:encodeURI($date),r_4d:encodeURI($4d),r_special:encodeURI($special),r_consolation:encodeURI($consolation),resource:'4d88'},function($data){
								try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}
								if($data.status==1){	
									jQuery('#result_container div:nth-child(n+100)').nextAll().remove();
									jQuery('<div style="margin:2px 0;padding:2px;border:1px solid #CCCCCC;"></div>').html($data.data).prependTo(jQuery('#result_container'));									
								}								
							}).complete(function(){ $self.count -= 1; });
							
							//SABAH 4D update
							$self.count += 1;
							var $table = $container.find('font:contains(SABAH 4D)').closest('table.curve');
							var $date = $table.find('strong font').eq(1).text();
							var $draw = $table.find('strong font').eq(2).text();							
							var $4d = $table.children('tbody').children('tr').eq(1).find('div').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==4){return $temp;}}).get().join("||");
							var $special = $table.find('strong:contains(Special)').closest('tbody').children('tr:gt(0)').find('td').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==4){return $temp;}}).get().join("||");
							var $consolation = $table.find('strong:contains(Consolation)').closest('tbody').children('tr:gt(0)').find('td').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==4){return $temp;}}).get().join("||");
							jQuery.get('/cronjob/ajax_result_update',{from:"sabah",title:"4d",draw:encodeURI($draw),date:encodeURI($date),r_4d:encodeURI($4d),r_special:encodeURI($special),r_consolation:encodeURI($consolation),resource:'4d88'},function($data){
								try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}
								if($data.status==1){	
									jQuery('#result_container div:nth-child(n+100)').nextAll().remove();
									jQuery('<div style="margin:2px 0;padding:2px;border:1px solid #CCCCCC;"></div>').html($data.data).prependTo(jQuery('#result_container'));									
								}								
							}).complete(function(){ $self.count -= 1; });
							
							//SABAH 3D update
							$self.count += 1;
							var $table = $container.find('font:contains(SABAH 3D)').closest('table.curve');
							var $date = $table.find('strong font').eq(1).text();
							var $draw = $table.find('strong font').eq(2).text();							
							var $3d = $table.children('tbody').children('tr').eq(1).find('div').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==3){return $temp;}}).get().join("||");							
							jQuery.get('/cronjob/ajax_result_update',{from:"sabah",title:"3d",draw:encodeURI($draw),date:encodeURI($date),r_3d:encodeURI($3d),resource:'4d88'},function($data){
								try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}
								if($data.status==1){	
									jQuery('#result_container div:nth-child(n+100)').nextAll().remove();
									jQuery('<div style="margin:2px 0;padding:2px;border:1px solid #CCCCCC;"></div>').html($data.data).prependTo(jQuery('#result_container'));									
								}								
							}).complete(function(){ $self.count -= 1; });							
							
							//SABAH LOTTO update
							$self.count += 1;
							var $table = $container.find('font:contains(SABAH LOTTO)').closest('table.curve').children('tbody').children('tr');
							var $date = $table.find('strong font').eq(1).text();
							var $draw = $table.find('strong font').eq(2).text();
							var $645 = $table.eq(1).find('tr.linesbox td').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length>=1 && $temp.length<=2){return $temp;}}).get().join(",");
							jQuery.get('/cronjob/ajax_result_update',{from:"sabah",title:"645",draw:encodeURI($draw),date:encodeURI($date),r_645:encodeURI($645),resource:'4d88'},function($data){
								try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}
								if($data.status==1){	
									jQuery('#result_container div:nth-child(n+100)').nextAll().remove();
									jQuery('<div style="margin:2px 0;padding:2px;border:1px solid #CCCCCC;"></div>').html($data.data).prependTo(jQuery('#result_container'));									
								}								
							}).complete(function(){ $self.count -= 1; });
							
							//SINGAPORE TOTO update
							$self.count += 1;
							var $table = $container.find('font:contains(SINGAPORE TOTO)').closest('table.curve').children('tbody').children('tr');
							var $date = $table.find('strong font').eq(1).text();
							var $draw = $table.find('strong font').eq(2).text();
							var $645 = $table.eq(1).find('tr.linesbox td').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length>=1 && $temp.length<=2){return $temp;}}).get().join(",");
							jQuery.get('/cronjob/ajax_result_update',{from:"singapore",title:"645",draw:encodeURI($draw),date:encodeURI($date),r_645:encodeURI($645),resource:'4d88'},function($data){
								try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}
								if($data.status==1){	
									jQuery('#result_container div:nth-child(n+100)').nextAll().remove();
									jQuery('<div style="margin:2px 0;padding:2px;border:1px solid #CCCCCC;"></div>').html($data.data).prependTo(jQuery('#result_container'));									
								}								
							}).complete(function(){ $self.count -= 1; });
							
							//damacai 6d update
							$self.count += 1;
							var $table = $container.find('font:contains(DAMACAI 6D)').closest('table.curve');
							var $date = $table.find('strong font').eq(2).text();
							var $draw = $table.find('strong font').eq(3).text();
							var $6d = $table.children('tbody').children('tr').eq(1).find('div').map(function(){var $temp=jQuery(this).text().replace(/[^\d\+]/gi,"");$temp=$temp.replace(/\+/gi,",");if($temp.length>0){return $temp;}}).get().join("||");
							var $special = $table.find('strong:contains(Starters)').closest('tbody').children('tr:gt(0)').find('td').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==6){return $temp;}}).get().join("||");
							var $consolation = $table.find('strong:contains(Consolation)').closest('tbody').children('tr:gt(0)').find('td').map(function(){var $temp=jQuery(this).text().replace(/\D/gi,"");if($temp.length==6){return $temp;}}).get().join("||");							
							jQuery.get('/cronjob/ajax_result_update',{from:"damacai",title:"6d",draw:encodeURI($draw),date:encodeURI($date),r_6d:encodeURI($6d),r_special:encodeURI($special),r_consolation:encodeURI($consolation),resource:'4d88'},function($data){
								try{$data = eval("(" + $data + ")");}catch(e){if(jQuery('#error_list').length>0){jQuery('#error_list').html($data);}else{jQuery('<div id="error_list"></div>').html($data).appendTo('body');}$data={status:0};}
								if($data.status==1){	
									jQuery('#result_container div:nth-child(n+100)').nextAll().remove();
									jQuery('<div style="margin:2px 0;padding:2px;border:1px solid #CCCCCC;"></div>').html($data.data).prependTo(jQuery('#result_container'));
								}								
							}).complete(function(){ $self.count -= 1; });
							
						}
					}				
				}).complete(function(){ $self.is_running = 6; });
			}
		}		
	}
</script>
<div>
	<img src="/application/globals/images/4d88.jpg" style="height:50px;">
</div>
<div style="color: #ff0000;font: bold 32px arial;padding:20px 0;">PLEASE DO NOT CLOSE THIS BROWSER!!!</div>
<div id="timing" style="padding:10px 0;"></div>
<div id="results">		
	Result:
	<div id="result_container"></div>	
</div>   
<div id="display" style="display:none;"></div>
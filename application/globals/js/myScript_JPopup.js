if(JFuncs==undefined){
	var JFuncs = {};
}

/*
 * JPopup
 *
 */
JFuncs.Popup = function(){
	this.id = 0;
	this.window = jQuery(window);
	this.container = jQuery('body');
	this.loading_img = "/application/globals/images/system/loading.gif";
	this.zindex = 999;
	this.min_zindex = 999;
	this.overlay = true;
	this.overlay_closable = true;
	this.overlay_cnt = 0;
	this.min_width = 200;
	this.min_height = 100;
	this.init();
}
JFuncs.Popup.prototype = {
	init:function(){
		var $self = this;
		jQuery(function(){			
			$self.initMask();			
			jQuery($self.window).resize(function($e){
				$self.setmiddle(jQuery('.Popup_overlay_container'),true);
				jQuery('.Popup_container.Popup_movable').each(function(){
					$self.getoverflow(jQuery(this));
				});
			});			
			jQuery(document).keyup(function(e) {
				if($self.overlay_cnt>0){					
					if (e.keyCode == 27){ // esc
						var $obj = jQuery('.Popup_overlay_container').last();
						if($obj.length>0){
							$self.close($obj);							
							return false;
						}						
					}
				}
				return true;
			});
		});
	},
	getid:function(){
		this.id += 1;		
		return this.id;
	},
	getzindex:function(){
		if(this.zindex==undefined || this.zindex==null || this.zindex<this.min_zindex){
			this.zindex = this.min_zindex;
		}
		this.zindex += 1;
		return this.zindex;
	},
	set_popup_focus:function($obj){
		var $self,$arraylist;
		$self = this;
		$arraylist = new Array;		
		$obj = jQuery($obj);
		jQuery('.Popup_container').each(function(){
			var $temp = jQuery(this);
			if($temp.attr('id')!=$obj.attr('id')){
				$arraylist[$arraylist.length] = {index:$temp.index(),id:$temp.attr('id')};			
			}			
		});				
		$arraylist.sort(function(a,b){return a.index-b.index});
		$arraylist[$arraylist.length] = {index:$obj.index(),id:$obj.attr('id')};
		for(var $i=0; $i<$arraylist.length; $i++){
			jQuery('#'+$arraylist[$i].id+'.Popup_container').css('z-index',$self.min_zindex+($i+1));
		}
		$self.zindex = $self.min_zindex + ($arraylist.length+1);
	},
	
	getoverflow:function($obj){
		var $self,$ew,$eh,$ex,$ey,$pos;
		$self = this;
		$pos = new Array;				
		$obj = jQuery($obj);
		$ew = $obj.outerWidth(true);
		$eh = $obj.outerHeight(true);
		$ex = $obj.position().left;
		$ey = $obj.position().top;				
		$pos = $self.check_overflow($ew,$eh,$ex,$ey,$obj);
		$obj.css('left',$pos[0]);
		$obj.css('top',$pos[1]);		
		return $pos;
	},
	check_overflow:function($ew,$eh,$ex,$ey,$obj){			
		var $self,$wx,$wy,$pos,$overflow,$padding;
		$self = this;
		$pos = new Array;
		$overflow = 0;
		$wx = jQuery($self.window).width();
		$wy = jQuery($self.window).height();
		$padding = $obj.outerHeight(true) - $obj.height();		
		//check position
		if($ex<0 || $ew>$wx){
			$pos[0] = 0;
			$overflow += 1;
		}else if(($ex+$ew)>$wx){
			$pos[0] = ($wx-$ew);
			$overflow += 2;
		}else{
			$pos[0] = $ex;
			$overflow += 3;
		}
		if($ey<0 || $eh>$wy){
			$pos[1] = 0;
			$overflow += 10;
		}else if(($ey+$eh)>$wy){
			$pos[1] = ($wy-$eh);
			$overflow += 20;
		}else{
			$pos[1] = $ey;
			$overflow += 30;
		}
		//check size
		if(($pos[0]+$ew)>$wx){
			$pos[2] = $wx - $padding;
		}else{
			$pos[2] = $ew;
		}
		if(($pos[1]+$eh)>$wy){
			$pos[3] = $wy - $padding;
		}else{
			$pos[3] = $eh;
		}		
		
		$pos[4] = $overflow;		
		
		return $pos;
	},
	getmiddle:function($obj,$fullscr){
		var $w_h,$w_w,$o_h,$o_w,$n_h,$n_w,$s_w,$s_h;
		$obj = jQuery($obj);
		$w_h = jQuery(this.window).height();
		$w_w = jQuery(this.window).width();
		$s_w = jQuery(this.window).scrollLeft();
		$s_h = jQuery(this.window).scrollTop();
		$o_h = $obj.outerHeight(true);
		$o_w = $obj.outerWidth(true);
		if($w_h<$o_h){
			$n_h = 0;
		}else{
			$n_h = Math.round(Math.abs(($w_h - $o_h) / 2));
		}
		if($w_w<$o_w){
			$n_w = 0;
		}else{
			$n_w = Math.round(Math.abs(($w_w - $o_w) / 2));
		}
		$n_h = $n_h + $s_h;
		$n_w = $n_w + $s_w;
		return {0:$n_w,1:$n_h};
	},
	setmiddle:function($obj,$fullscr){
		$obj = jQuery($obj);
		var $pos = this.getmiddle($obj,$fullscr);		
		$obj.css({'top':$pos[1],'left':$pos[0]});
	},
	setLoading:function($element){
		var $self,$loading,$content;
		$self = this;
		$element = jQuery($element);		
		$element.css({width:$element.outerWidth(true),height:$element.outerHeight(true)});
		$content = $element.find('#Popup_content');		
		$loading = jQuery('#Popup_default_loading').clone();
		$loading.attr({'id':null,'class':'Popup_loading'}).css({'display':'block'}).show();
		if($content!=undefined && $content.length){				
			$content.html($loading);
		}else{
			$element.html($loading);
		}		
	},
	move:function($event,$obj){
		var $self,$frame,$x,$y;
		$self = this;
		$obj = jQuery($obj);		
		$frame = jQuery('<div></div>').css({'width':$obj.outerWidth(true)-6,'height':$obj.outerHeight(true)-6,'top':$obj.position().top,'left':$obj.position().left,'background':'transparent','border':'3px solid #000fff','position':'absolute','z-index':$self.getzindex()});
		jQuery(document).bind('mousemove',function($e){
			$e.preventDefault();
			$x = $obj.position().left + ($e.pageX - $event.pageX);
			$y = $obj.position().top + ($e.pageY - $event.pageY);
			$frame.css({'top':$y,'left':$x});
			$self.getoverflow($frame);
		});
		jQuery(document).bind('mouseup',function($e){
			$e.preventDefault();
			jQuery(document).unbind('mousemove');
			jQuery(document).unbind('mouseup');			
			$obj.css({'top':$frame.position().top,'left':$frame.position().left});
			$self.set_popup_focus($obj);			
			$frame.stop(true).remove();
		});
		$frame.appendTo($self.container);
		return true;
	},
	initMask:function(){
		var $temp;
		if(jQuery('#Popup_default_loading').length==0){
			$temp = jQuery('<div id="Popup_default_loading" style="display:none;"><table width="100%" height="100%"><tr><td valign="center" align="center"><img src="'+this.loading_img+'" width="30"></td></tr></table></div>');
			$temp.appendTo(this.container);
		}
		if(jQuery('#Popup_default_overlay').length==0){
			$temp = jQuery('<div id="Popup_default_overlay" style="display:none;"></div>');
			$temp.appendTo(this.container);
		}
		if(jQuery('#Popup_default_container').length==0){
			/*
			$temp = jQuery('<div id="Popup_default_container" style="display:none;"><table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%"><tr><td><div class="tl"></div></td><td><div class="tc"></div></td><td><div class="tr"></div></td></tr><tr><td><div class="cl"></div></td><td class="cc" style="width:100%;height:100%;"><table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%"><tr class="title_menu"><td align="left" valign="top" class="Popup_container_title"></td><td align="right" valign="top" class="Popup_container_close"></td></tr><tr><td align="center" valign="center" colspan="2" style="background:#FFFFFF;"><div id="Popup_content"></div></td></tr></table></td><td><div class="cr"></div></td></tr><tr><td><div class="bl"></div></td><td><div class="bc"></div></td><td><div class="br"></div></td></tr></table></div>');
			*/
			$temp = jQuery('<div id="Popup_default_container" style="display:none;"><table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%"><tr><td class="tl"></td><td class="tc"></td><td class="tr"></td></tr><tr><td class="cl"><div></div></td><td class="cc" align="center" valign="center" style="width:100%;height:100%;"><table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%"><tr class="title_menu"><td width="100%" align="left" valign="top"><div class="Popup_container_title"></div></td><td align="right" valign="top"><div class="Popup_container_close"></div></td></tr><tr><td align="center" valign="center" colspan="2" style="background:#FFFFFF;"><div id="Popup_content"></div></td></tr></table></td><td class="cr"><div></div></td></tr><tr><td class="bl"></td><td class="bc"></td><td class="br"></td></tr></table></div>');			
			$temp.appendTo(this.container);
		}
		if(jQuery('#Popup_default_sidebutton').length==0){
			$temp = jQuery('<div class="Popup_sidebutton" id="Popup_default_sidebutton" style="display:none;"><div id="title"></div></div>');
			$temp.appendTo(this.container);
		}
		//this.createSidebar();
	},
	createSidebar:function(){
		if(jQuery('.Popup_sidebar').length==0){
			$temp = jQuery('<div class="Popup_sidebar"><div id="Popup_sidebar_container"></div><div id="Popup_line"></div></div>');
			$temp.appendTo(this.container);
		}
		if(jQuery('#Popup_default_sidebutton').length==0){
			$temp = jQuery('<div class="Popup_sidebutton" id="Popup_default_sidebutton" style="display:none;"><div id="title"></div></div>');
			$temp.appendTo(this.container);
		}
		jQuery('.Popup_sidebar').each(function(){
			var $obj = jQuery(this);
			$obj.mouseover(function(){
				$obj.stop(true).animate({'left':0,'opacity':'1'},20);
			});
			$obj.stop(true).mouseout(function(){
				$obj.stop(true).animate({'left':(($obj.find('#Popup_sidebar_container').outerWidth(true))*-1),'opacity':'0.1'},400);
			});
			$obj.stop(true).mouseout();
		});
	},
	createSidebutton:function($element){
		var $sidebar,$sidebutton;
		$element = jQuery($element);
		$sidebar = jQuery('.Popup_sidebar');
		if($sidebar.length>0){
			$sidebutton = jQuery('#Popup_default_sidebutton').clone();
			$sidebutton.click(function(){
				$element.fadeIn('fast');
				$sidebutton.remove();
			});
			$sidebutton.attr({'id':$element.attr('id')});
			$sidebutton.find('#title').html($element.find('.Popup_container_title').html());
			jQuery($sidebutton).appendTo($sidebar.find('#Popup_sidebar_container')).fadeIn('fast');
			$element.fadeOut('fast');
		}
	},
	createContainer:function($overlay){
		var $self,$id,$element;
		$self = this;
		$id = this.getid();
		$element = jQuery('#Popup_default_container').clone();
		$element.attr({'id':'Popup_id_'+$id,'class':'Popup_container'}).css({'display':'block','z-index':this.getzindex()}).fadeOut();
		$element.find('.Popup_container_close').click(function(){
			$self.close($element);
		});
		if($overlay==true){
			$element.addClass('Popup_overlay_container');
			$element.find('.Popup_container_minimize').hide();
		}else{
			$element.addClass('Popup_movable');
			if(jQuery('.Popup_sidebar').length){
				$element.find('.Popup_container_minimize').show().click(function(){
					$self.createSidebutton($element);
				});
			}			
			$element.find('.Popup_container_title').bind('mousedown',function($e){
				$e.preventDefault();
				$self.move($e,$element);
			});			
		}
		$element.css({width:$self.min_width+'px',height:$self.min_height+'px'});
		$element.appendTo(this.container);
		this.setLoading($element);
		this.setmiddle($element);
		return $element;
	},
	createOverlayContainer:function(){
		var $self,$id,$element,$overlay;
		$self = this;
		$id = this.getid();
		$overlay = jQuery('#Popup_default_overlay').clone();
		$overlay.attr({'id':'Popup_overlayid_'+$id,'class':'Popup_overlay'}).css({'z-index':this.getzindex()});
		if($self.overlay_closable){
			$overlay.click(function(){
				$self.close(this);
			});
		}
		$overlay.appendTo(this.container);
		$element = this.createContainer(true);
		$overlay.attr('cid',$element.attr('id'));
		return $element;
	},
	close:function($obj){
		$obj = jQuery($obj);
		if($obj.length==0){
			return false;
		}
		var $self = this;
		var $data,$cid;
		$data = $obj.data('data');
		$obj.removeData('data');
		if($data){
			var $prog = JFunc.run($data.prog);
			if($prog){
				$prog.cancel($data.id);
			}
		}
		$cid = $obj.attr('cid');
		if($cid!=undefined){
			$self.close(jQuery('#'+$cid+'.Popup_container'));
		}else{
			jQuery($obj).stop(true).fadeOut('fast', function(){
				jQuery(this).remove();
			});
			jQuery('[cid="'+$obj.attr('id')+'"].Popup_overlay').stop(true).fadeOut('fast', function(){
				jQuery(this).remove();				
				$self.overlay_cnt -= 1;
			});
		}

		return true;
	},
	open:function($o,$overlay,$html){		
		if(this.overlay_cnt>0){
			$overlay = true;
			//return false;
		}		
		if($overlay==undefined){
			$overlay = this.overlay;
		}
		var $self,$element;
		$self = this;
		if($overlay==true){			
			this.overlay_cnt += 1;
			$element = this.createOverlayContainer();
		}else{
			$element = this.createContainer();
		}
		$element.data('data',$o);		
		jQuery('[cid="'+$element.attr('id')+'"].Popup_overlay').css({'opacity':'0.1'}).fadeIn('fast');
		$element.fadeIn('fast');
		if($html!=undefined && $html.title!=undefined && $html.title.length>0){
			var $title = $html.title;
			if($title.length>13){
				$title = $title.substr(0,10)+"...";
			}
			$element.find('.Popup_container_title').html($html.title);
		}else{
			$element.find('.Popup_container_title').html("");
		}
		if($html!=undefined && $html.data!=undefined && $html.data.length>0){
			//$element.find('#Popup_content').html($html.data);
			this.show($element.attr('id'),"",$html.data);
		}else if($html!=undefined && $html.length>0){			
			this.show($element.attr('id'),"",$html);
		}
		return $element.attr('id');
	},
	show:function($id,$textStatus,$data){
		if($id==undefined){
			return false;
		}
		var $self,$element,$content,$js_owner,$js_ownerid,$html,$js;
		$self = this;
		$js_owner = /\$\$owner/mig;
		$js_ownerid = /\$\$owner_id/mig;

		if($data==undefined){
			$data = "";
		}		
		if(document.getElementById($id)==null){
			return false;
		}
		$element = jQuery('#'+$id).stop(true);
		$content = $element.find('#Popup_content');
		$html = "";
		if($data.data!=undefined && typeof $data.data=="string"){
			$html = $data.data;
		}else if(typeof $data=="string"){
			$html = $data;
		}
		if($data.javascript!=undefined){
			for($js in $data.javascript){
				$data.javascript[$js] = $data.javascript[$js].replace($js_ownerid,'#'+$id+'.Popup_container');
				$data.javascript[$js] = $data.javascript[$js].replace($js_owner,$id);
			}
		}
		$html = $html.replace($js_ownerid,'#'+$id+'.Popup_container');
		$html = $html.replace($js_owner,$id);

		if($content!=undefined && $content.length){
			$content.fadeOut('fast',function(){
				var $pos = $self.get_buffer($element,jQuery('<div></div>').html($html));
				$element.animate({'top':$pos[1],'left':$pos[0],'width':$pos[2],'height':$pos[3]},'fast','linear',function(){					
					$content.html($html);					
					$content.fadeIn('fast');
//					if($content.html().length>0){
//						$element.css({width:'auto',height:'auto'});
//					}
					if($data.javascript!=undefined){
						for(var $js in $data.javascript){
							$content.append($data.javascript[$js]);
						}
					}
					$self.re_run($content);					
				});
			});
		}else{
			$element.html($html);
			if($data.javascript!=undefined){
				for($js in $data.javascript){
					$content.append($data.javascript[$js]);
				}
			}
			$self.re_run($element);
		}
		return true;
	},
	re_run:function($element){
		$element = jQuery($element);
		setTimeout(function(){
			if(JFunc.JScript!=undefined){
				JFunc.JScript.exec($element);
			}
			if(JFunc.DValidation!=undefined){
				JFunc.DValidation.initBtn($element);
			}
		},500);
	},
	get_buffer: function($element,$obj){
		var $self,$pos,$buffer,$title,$padding,$tobj;
		$self = this;
		$element = jQuery($element);
		$obj = jQuery($obj);		
		
		$buffer = $element.clone().hide().appendTo('body');
		$tobj = jQuery('<div></div>').hide().html($obj).appendTo('body');
		$buffer.css({width:($buffer.outerWidth(true)-$buffer.width())+$tobj.width(),height:($buffer.outerHeight(true)-$buffer.height())+$tobj.height()});
		if($buffer.outerWidth(true)<$self.min_width){
			$buffer.css('width',$self.min_width+'px');
		}
		if($buffer.outerHeight(true)<$self.min_height){
			$buffer.css('height',$self.min_height+'px');
		}
		
		if($element.hasClass('Popup_overlay_container')){			
			$pos = JFunc.Popup.getmiddle($buffer,true);
//			$pos = $self.check_overflow($buffer.outerWidth(true),$buffer.outerHeight(true),$pos[0],$pos[1],$buffer);
		}else{
			$pos = JFunc.Popup.getmiddle($buffer,true);
			$pos[0] = $element.position().left;
			$pos[1] = $element.position().top;
//			$pos = $self.check_overflow($buffer.outerWidth(true),$buffer.outerHeight(true),$element.position().left,$element.position().top,$buffer);			
		}
		$pos[2] = $buffer.outerWidth(true);
		$pos[3] = $buffer.outerHeight(true);
		
		$tobj.remove();
		$buffer.remove();
		return $pos;
	}
}
jQuery(function(){
	JFunc.run('Popup');
});

/*
 * Initialize
 *
 */
if(JFunc==undefined){
	var JFunc = {
		runner:[],
		run: function($name){
			if(jQuery.isFunction(JFuncs[$name])){
				if(this[$name]==undefined){
					return this[$name] = new JFuncs[$name];
				}else{
					return this[$name];
				}

			}
			return false;
		}
	}
}
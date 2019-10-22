cpage = function($o){
	this.options = {};
	this.options = jQuery.extend({},this.options,$o);
}
cpage.prototype = {		
	update_system_status:function($text){
		var $self,$obj;
		$self = this;
		if($text==undefined || $text==""){
			return;
		}		
		$obj = jQuery('.message_module');
		if($obj){
			$obj.html(jQuery($text));
		}		
	}
};
jQuery(function(){
	var $cpage = new cpage();
});

jQuery(function(){
	var $t;
	$t = jQuery('#tool_tips');
	if($t.length==0){
		$t = jQuery('<div id="tool_tips"></div>');
		jQuery('body').append($t);
	}		
	jQuery('.show_tool_tips').hover(
	function(){
		var $nw,$nh,$w,$obj;
		$obj = jQuery(this);
		$w = jQuery(window);
		$t.html($obj.attr('desc'));		
		if($obj.position().left+$t.outerWidth()<=0){
			$nw = 0;
		}else if($obj.position().left+$t.outerWidth()>$w.width()){			
			$nw = $w.width()-$t.outerWidth();
		}else{
			$nw = $obj.position().left;
		}		
		if($obj.position().top+$obj.outerHeight()+5<=0){
			$nh = 0;
		}if($obj.position().top+$obj.outerHeight()+5>$w.height()){
			$nh = $w.height()-$t.outerHeight();
		}else{
			$nh = $obj.position().top+$obj.outerHeight()+5;
		}
		$t.css({left:$nw,top:$nh});
		$t.show();
	},
	function(){
		$t.hide();
	});
});

jQuery(function(){
	jQuery('.control_panel a').hover(
	function(){
		var $obj = jQuery(this);
		$obj.find('div').addClass('hover');			
	},
	function(){
		var $obj = jQuery(this);
		$obj.find('div').removeClass('hover');			
	});
});
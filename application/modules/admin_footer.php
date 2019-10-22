<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
?>

<!--<script>		
	var $footer_func = {
		init:function(){			
			var $self = this;
			var $temp = $self;
			jQuery(window).resize(function(){
				$self.move();
			});		
			$self.move();
		},
		move:function(){
			var $self = this;
			var $w = jQuery(window);
			var $c = jQuery('#contents');
			var $f = jQuery('#admin_footer');
			//$f.css('width',$c.width()+'px');
			if($c.outerHeight()+$f.outerHeight()<$w.height()){
				$f.css({'position':'absolute'});
			}else{				
				$f.css({'position':'relative'});
			}
			$f.show();
		}
	};
	jQuery(function(){
		$footer_func.init();		
	});
</script>	-->
<script>
	jQuery(function(){
		jQuery('#contents').css('padding','0 0 30px 0');
	});	
</script>

<div id="admin_footer" style="position:fixed;bottom:0;left:0;text-align:center;height:20px;width:100%;padding:20px 0 5px 0;background:url('/application/templates/default/images/admin_footer_bg.png') repeat-x 0% 0% #a0a0a0;">
	<div style="color: #f2f2f2;font:14px arial;">
		Copyright @ 2011
	</div>
</div>

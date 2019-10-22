listTemplate = function($o){
	this.img_list = {};
	this.img_list = jQuery.extend({},this.img_list,$o);
}
listTemplate.prototype = {	
	form_search_validate:function(){
		var $obj,$text,$filter,$field,$ourl,$btn;
		$obj = jQuery('form#form_search');	
		$btn = $obj.find('#btnSearch');
		$ourl = $obj.find('#ourl').val();
		if($btn.val()=="Search"){			
			$field = new Array();
			$text = new Array();
			$obj.find('.group').each(function(){				
				var $group = jQuery(this);
				var $tt = $group.find('.search_query');
				var $tf = $group.find('.search_filter');
				if($tt.length>0 && $tf.length>0){					
					var $tfv = "";
					var $ttv = $tt.val();
					if($tf.is('select')){
						$tfv = $tf.find('option:selected').val();						
					}else{
						$tfv = $tf.val();						
					}
					if($tfv.length>0 && $ttv.length>0){
						$field.push(encodeURI("{"+$tfv+"},"));
						$text.push(encodeURI("{"+$ttv+"},"));
					}					
				}				
			});
			if($field.length==0){
				$field.push(encodeURI("{"+$obj.find('.search_filter').find('option:selected').val()+"},"));
				$text.push(encodeURI("{"+$obj.find('.search_query').val()+"},"));
			}			
			location.href=$ourl+"&ff="+$field.join("")+"&fv="+$text.join("")+"&fo=and";
		}else{
			location.href=$ourl+"&ff=&fv=";
		}			
		return false;
	},
	update_img_status:function($name,$id,$value){
		var $obj,$obj_id,$img_id;
		$obj_id = $name+"_"+$id;		
		if($value==undefined){
			$value = 0;
		}
		$img_id = $name+"_"+$value;
		$obj = jQuery('#'+$obj_id+' img');		
		if($obj && this.img_list[$img_id]!=undefined){
			$obj.attr("src",this.img_list[$img_id]);
		}
	}
};

jQuery(function(){
	jQuery('.table_list tr').each(function(){
		var $obj = jQuery(this);
		$obj.attr('ori_color',$obj.css('background-color'));
		$obj.hover(
		function(){
			$obj.css({'background-color':'#f5eabe'});
		},
		function(){
			$obj.css({'background-color':$obj.attr('ori_color')});
		});
	});
	jQuery('.table_list a.func').each(function(){
		var $obj = jQuery(this);
		$obj.attr('url',$obj.attr('href'));
		$obj.attr('href','javascript:void(0)');
		$obj.click(function(){
			JFunc.Jax.link($obj.attr('url'),true);			
		});
	});
	jQuery('.table_list a.dialog').each(function(){
		var $obj = jQuery(this);
		$obj.attr('url',$obj.attr('href'));
		$obj.attr('href','javascript:void(0)');
		$obj.click(function(){			
			JFunc.Jax.dialog($obj.attr('url'));
		});
	});
	jQuery('.form_chk_parent').click(function(){			
		var $obj = jQuery(this);
		if($obj.is(':checked')){
			$obj.closest('.table_list').find('.form_chk_node').attr('checked','checked');				
		}else{
			$obj.closest('.table_list').find('.form_chk_node').removeAttr('checked');
		}			
	});
	jQuery('#btnDelete').click(function(){						
		var $form = jQuery(this).closest('form');
		if($form.length){
			if($form.find('.form_chk_node:checked').length>0){
				if(window.confirm("Are you confirm to delete?")){
					$form.submit();
				}
			}else{
				alert("Please select the item to delete");
			}
		}			
	});
	jQuery('.filter_list').each(function(){
		var $obj = jQuery(this);
		var $container = $obj.closest('.filter_container');
		//if($obj.find('.search_query').length==0 || $obj.find('.search_query').map(function(){return jQuery(this).val()}).get().join("").length){
		if($obj.find('.search_query').length==0){
			$container.show();
		}else{
			$container.hide();
		}
	});	
	jQuery('.filter_link').click(function(){
		var $link = jQuery(this);
		var $container = $link.closest('table').find('.filter_container');
		if($link.length>0 && $container.length>0){
			$container.slideToggle('fast');		
		}
	});	
});
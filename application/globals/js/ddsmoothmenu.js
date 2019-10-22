var ddsmoothmenu={
	option: {
		mainmenuid: "smoothmenu", //menu DIV id
		orientation: 'h',
		classname: 'ddsmoothmenu',
		arrowimages: {
			up: 'arrow_up',
			down: 'arrow_down',
			left: 'arrow_left',
			right: 'arrow_right'
		}
	},	

	buildmenu:function($, setting){
		var smoothmenu= ddsmoothmenu
		setting = jQuery.extend(smoothmenu.option,setting);
		var $mainmenu=$("#"+setting.mainmenuid+">ul") //reference main menu UL
		$mainmenu.parent().get(0).className=setting.classname || "ddsmoothmenu"
		$mainmenu.find('li').hover(
			function(e){				
				$(this).addClass('selected')
			},
			function(e){
				$(this).removeClass('selected')
			}
		).each(function(i){
			var $curobj=$(this).css({zIndex: 1000+i}); //reference current LI header
			var $subul=$curobj.find('ul:eq(0)').css({display:'block'});			
			this.istopheader=$curobj.parents("ul").length==1? true : false; //is top level header?			
			this._dimensions={w:(this.istopheader)?$curobj.innerWidth():$curobj.parent().innerWidth(), h:(this.istopheader)?$curobj.innerHeight():$curobj.parent().innerHeight(), subulw:$subul.outerWidth(), subulh:$subul.outerHeight()};
			if($subul.length>0){
				$subul.css({top:this.istopheader && setting.orientation!='v'? this._dimensions.h+"px" : 0});
				if(setting.orientation!='v'){
					if(this.istopheader){
						$curobj.addClass(setting.arrowimages.down);
					}else{
						$curobj.addClass(setting.arrowimages.right);
					}				
				}else{
					if(this.istopheader){
						$curobj.addClass(setting.arrowimages.right);
					}else{
						$curobj.addClass(setting.arrowimages.right);
					}				
				}
					
			}
			
			if(!this.istopheader && $curobj.attr('ico')!=undefined && $curobj.attr('ico').length>0){
				$curobj.children('a:eq(0),span:eq(0)').prepend(jQuery('<img src="'+$curobj.attr('ico')+'" style="padding:0;margin:0;border:0;width:16px;height:16px;">'));				
			}
			
			$curobj.hover(
				function(e){					
					var $targetul=$subul //reference UL to reveal					
					var header=$curobj.get(0) //reference header LI as DOM object					
					header._offsets={left:$curobj.offset().left, top:$curobj.offset().top}
					var menuleft=header.istopheader && setting.orientation!='v'? 0 : header._dimensions.w
					menuleft=(header._offsets.left+menuleft+header._dimensions.subulw>$(window).width())? (header.istopheader && setting.orientation!='v'? -header._dimensions.subulw+header._dimensions.w : -header._dimensions.w) : menuleft //calculate this sub menu's offsets from its parent
					$targetul.css({left:menuleft+"px", width:header._dimensions.subulw+'px'}).stop().show();
				},
				function(e){
					var $targetul=$subul;
					$targetul.stop().hide();
				}
			) //end hover	
		});
		$mainmenu.find('ul').css({display:'none', visibility:'visible'});
	},

	init:function(setting){	
		jQuery(document).ready(function($){
			ddsmoothmenu.buildmenu($, setting);
		})
	}

} //end ddsmoothmenu variable

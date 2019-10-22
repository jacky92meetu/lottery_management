if(JFuncs==undefined){
	var JFuncs = {};
}

/*
 *	Dvalidation
 *	example:
 *  <input name="email" type="text" id="email" size="35" class="DValidation" vtype="email" vmsg="Please enter your valid email address" />
 *  <input name="username" type="text" id="username" size="35" maxlength="12" class="DValidation" vtype="required strlen:2 user_register" vmsg="required::Please enter user id||strlen::User id too short||user_register::User id start with LV_ is not allow" />
 *
 *  DValidation_focus -> assign to focus the element
 *  DValidation_button -> assign to button or submit
 */
JFuncs.DValidation = function(){
	this.is_error = 0;
	this.btn_name = 'DValidation_button';
	this.loading_button = "/application/globals/images/system/wait.gif";
	this.loading_img = "/application/globals/images/system/loading.gif";
	this.error_color = "#FF9797";
	this.type = {
		checked:function($obj){
			if($obj.is(':checked')){
				return true;
			}
			return false;
		},
		required:function($obj){
			var $value = jQuery.trim($obj.val());
			if($value!=null && $value!=undefined && $value!=''){
				return true;
			}
			return false;
		},
		strlen:function($obj,$func){
			if($func.length>=2){
				if($func.length==3){
					if($obj.val().length>=$func[1] && $obj.val().length<=$func[2]){
						return true;
					}
				}else{
					if($obj.val().length>=$func[1]){
						return true;
					}
				}
			}

			return false;
		},
		eq:function($obj,$func){
			if($func.length==2){
				if($obj.val()==$func[1]){
					return true;
				}
			}

			return false;
		},
		eqlen:function($obj,$func){
			if($func.length==2){
				if($obj.val().length==$func[1]){
					return true;
				}
			}

			return false;
		},
		match:function($obj,$func){
			if($func.length==2){
				var $form = $obj.closest('form');
				if($form.length && $form.find('#'+$func[1]).val()==$obj.val()){
					return true;
				}
			}

			return false;
		},
		between:function($obj,$func){
			if($func.length>=2){
				if($func.length==3){
					if($obj.val()>=$func[1] && $obj.val()<=$func[2]){
						return true;
					}
				}else{
					if($obj.val()>=$func[1]){
						return true;
					}
				}
			}

			return false;
		},
		email:function($obj){
			regex=/^[a-zA-Z0-9._-]+@([a-zA-Z0-9.-]+\.)+[a-zA-Z0-9.-]{2,4}$/;
			isValid = regex.test($obj.val());
			if(isValid){
				return true;
			}
			return false;
		},
		user_register:function($obj){
			regex=/^[a-zA-Z0-9]+[a-zA-Z0-9-]*$/i;
			if(regex.test($obj.val())){
				return true;
			}
			return false;
		},
		time:function($obj){
			var $value = $obj;
			if((typeof $obj == "object")){
				$value = $obj.val();
			}
			regex=/^((([0-1]{1})?[0-9]{1})|(2[0-3]{1})):(([0-5]{1})?[0-9]{1})(:(([0-5]{1})?[0-9]{1}))?$/;
			isValid = regex.test($value);
			if(isValid){
				return true;
			}
			return false;
		},
		date:function($obj){
			var $value = $obj;
			if((typeof $obj == "object")){
				$value = $obj.val();
			}
			regex=/^([1-9]{1}[0-9]{3})-((0?[1-9]{1})|(1[0-2]{1}))-((0?[1-9]{1})|(([1-2]{1})[0-9]{1})|(3[0-1]{1}))$/;
			isValid = regex.test($value);
			if(isValid){				
				var $temp = $value.split("-");
				var $date = new Date(($temp[0]*1),($temp[1]*1-1),($temp[2]*1));				
				if($date!=undefined && $date!=null){					
					var $tyear = $date.getFullYear();
					var $tmonth = $date.getMonth()+1;
					var $tdate = $date.getDate();
					if(($tyear*1)==($temp[0]*1) && ($tmonth*1)==($temp[1]*1) && ($tdate*1)==($temp[2]*1)){
						return true;
					}					
				}
			}
			return false;
		},
		datetime:function($obj){			
			var $data = $obj.val().split(" ");
			if($data.length){				
				if($data.length==1){
					if(this.date($data[0])){
						return true;
					}
				}else if($data.length==2){
					if(this.date($data[0]) && this.time($data[1])){
						return true;
					}
				}				
			}
			return false;
		},
		regexp:function($obj,$func){
			var $value = $obj;
			if((typeof $obj == "object")){
				$value = $obj.val();
			}
			regex = eval('('+$func[1]+')');
			isValid = regex.test($value);
			if(isValid){
				return true;
			}
			return false;
		}
	};

	this.init();
}
JFuncs.DValidation.prototype={
	check:function($button){
		if($button.is('form')){
			var $t = jQuery($button).find(':input[type="submit"]');
			$button = $t;
		}
		$button = jQuery($button);
		var $form = $button.closest('form');

		if($form.length){
			var $self = this;
			var $errno = 0;
			if($button.attr('dwc')==undefined){
				$form.find(':input.DValidation').each(
					function(){
						var $obj = jQuery(this);
						var $result = 0;
						var $type_list = $obj.attr('vtype').split(' ');
						$self.hideErrMsg($obj);
						for(var $i=0; $i<$type_list.length; $i++){
							$func = $type_list[$i].split(':');
							if(typeof $self.type[$func[0]] == 'function') {								
								if(!$self.type[$func[0]]($obj,$func)){
									$result = $result + 1;
									$self.showErrMsg($obj,$func[0]);
									break;
								}
							}
						}
						if($result>0){
							$errno = $errno + 1;
							if($errno==1){
								$obj.select();
							}
						}
					}
				);
			}

			if($errno==0){
				if($self.proceed($button,$form)){
					return true;
				}
			}
		}

		return false;
	},
	proceed:function($button,$form){
		if($button.attr('cvalue')!=undefined){
			var $cvalue_list = $button.attr('cvalue').split('||');
			for(var $i=0; $i<$cvalue_list.length; $i++){
				var $v = $cvalue_list[$i].split(':');
				if($v.length==2){
					$form.find('#'+$v[0]).val($v[1]);
				}
			}
		}
		if($button.attr('func')!=undefined){
			var $func = $button.attr('func');
			/*
			if(typeof $func === 'function'){
				$func();
			}else{
				eval($func);
			}
			*/
			eval($func);
			return false;
		}
		this.setMask();
		//this.disableButton($button);
		return true;
	},
	disableButton:function($button){
		$button.hide();
		$button.attr('disabled',true);
		jQuery('#Dvalidation_button_overlay').clone().insertBefore($button).fadeIn('fast');
	},
	setMask:function(){
		jQuery('#Dvalidation_mask_overlay').clone().css({'opacity':'0.5'}).appendTo('body').fadeIn('fast');
	},
	initMask:function(){
		if(jQuery('#Dvalidation_button_overlay').length==0){
			jQuery('<div id="Dvalidation_button_overlay" style="display:none;"><img src="'+this.loading_button+'"></img></div>').appendTo('body');
		}
		if(jQuery('#Dvalidation_mask_overlay').length==0){
			var $t1 = jQuery('<div id="Dvalidation_mask_overlay" style="display:none;"></div>').appendTo('body');
			$t1.append(jQuery('<div id="Dvalidation_mask_background"></div>'));
			$t1.append(jQuery('<div id="Dvalidation_mask_contents"><table width="100%" height="100%"><tr><td align="center" valign="center"><img src="'+this.loading_img+'"></img></td></tr></table></div>'));
		}
	},
	hideErrMsg:function($obj,$func){
		if($obj){
			if($func!=undefined){
				jQuery($obj).parent().find('[vtype~="'+$func+'"].DValidation_msg').hide().removeClass('invalid');
			}else{
				jQuery($obj).parent().find('.DValidation_msg').hide().removeClass('invalid');
			}
			jQuery($obj).css({'background-color':jQuery($obj).data('cur-color')});
		}else{
			jQuery(':input.DValidation').each(function(){
				var $input = jQuery(this);
				$input.css({'background-color':$input.data('cur-color')});
			});
			jQuery('.DValidation_msg').hide().removeClass('invalid');
		}
	},
	showErrMsg:function($obj,$func){
		var $self;
		$self = this;
		if($obj){
			if($func!=undefined){
				jQuery($obj).parent().find('[vtype~="'+$func+'"].DValidation_msg').fadeIn().addClass('invalid');
			}else{
				jQuery($obj).parent().find('.DValidation_msg').fadeIn().addClass('invalid');
			}
			jQuery($obj).css({'background-color':$self.error_color});
			jQuery('.DValidation_msg:first:visible').focus();			
		}else{
			jQuery(':input.DValidation').each(function(){
				var $input = jQuery(this);
				$input.css({'background-color':$self.error_color});
			});
			jQuery('.DValidation_msg').fadeIn().addClass('invalid');
		}
	},
	createErrMsg:function($form){
		var $self,$err;
		$self = this;
		$err = 0;
		jQuery($form).find(':input.DValidation').each(
			function(){
				var $obj = jQuery(this);
				if($obj.attr('name')!=undefined && $obj.attr('vtype')!=undefined && $obj.attr('vmsg')!=undefined){
					var $name = $obj.attr('name');
					var $type = $obj.attr('vtype');
					var $msg = $obj.attr('vmsg');	
					if($name.length && $type.length && $msg.length){
						var $msg_list = $msg.split('||');
						for(var $i=0; $i<$msg_list.length; $i++){
							var $type2;
							var $msg2;
							var $func = $msg_list[$i].split('::');
							if($func.length==1){
								$func = $type.split(':');
								$type2 = $func[0];
								$msg2 = $msg;
							}else{
								$type2 = $func[0];
								$msg2 = $func[1];
							}
							if($msg2.length>0){
								$obj.after(
								jQuery('<div class="DValidation_msg" vtype="'+$type2+'" id="err'+$name+'_'+$type2+'msg" style="font-family:Arial, Helvetica, sans-serif; font-size:11px; font-weight:bold; color:'+$self.error_color+';">'+$msg2+'</div>').hide()
								);
							}
						}
						$obj.after(jQuery('<font style="color:#ff0000;font:bold 24px arial;"> * </font>'));
					}
				}
				jQuery($obj).data('cur-color',jQuery($obj).css('background-color'));				
			}
		)

		if($err==0){
			return true;
		}

		return false;
	},
	initBtn:function($element){
		var $self = this;
		$element = jQuery($element);
		if($element.length){
			$element.find('.DValidation_focus').focus();
			$element.find('.DValidation_button').each(function(){
				var $obj,$form;
				$obj = jQuery(this);
				$form = $obj.closest('form');
				if($form.length){
					$self.createErrMsg($form);					
					if($obj.attr('func')==undefined && $form.attr('onSubmit')!=undefined){
						$obj.attr('func',$form.attr('onSubmit'));						
					}
					$form.removeAttr('onSubmit');
					if($obj.attr('func')==undefined && $obj.attr('onClick')!=undefined){						
						$obj.attr('func',$obj.attr('onClick'));						
					}
					$obj.removeAttr('onClick');
					if(($form.attr('action')==undefined || $form.attr('action')=="") && $obj.attr('func')!=undefined){
						$form.attr('onSubmit','return false;');
					}
					if($obj.attr('type').toLowerCase()=='submit'){
                        $form.submit(function(){
                            return $self.check($form);
                        });
					}else{
						$obj.each(function(){							
							jQuery(this).removeAttr('onClick').click(
								function(){
									if($self.check($obj) && $obj.attr('onClick')==undefined){
										$form[0].submit();
									}
								}
							);
						});
					}
				}
			});
		}
	},
	init:function(){
		var $self = this;
		jQuery(function(){
			$self.initMask();
			$self.initBtn(jQuery('body'));
		});
	}
}
jQuery(function(){
	JFunc.run('DValidation');
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
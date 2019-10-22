if(JFuncs==undefined){
	var JFuncs = {};
}

/*
 * Jax
 *
 */
JFuncs.Jax = function(){
	this.xhrs = [];
	this.id = 0;
	this.link_name = 'Jax_get_link';
	this.loading_mask_class = 'Jax_loading_overlay';
	this.enable_ajax = 1;
	this.loading_img = "/application/globals/images/system/loading.gif";
	this._options = {
		_form_submit:0,
		_id:null,
		_data:{},
		_formid:null,
		_fileid:null,
		_fired:null,
		_obj:null,
		_action:null,
		_type:"post",
		_onProgress:function($options, $loaded, $total){},
		_error:function($options, $textStatus, $errorThrown){
			this._unsetLoading($options);
		},
		_return:function($options,$status,$data){
			if(typeof $data=="object" && $data.status==1){
				if($options._obj!=null && $options._obj!=undefined && $data.data!=undefined){
					var $obj = jQuery('#'+$options._obj);
					if($obj.length){
						$obj.children().unbind();
						$obj.html($data.data);
						if($data.javascript!=undefined){
							for(var $js in $data.javascript){
								$obj.append($data.javascript[$js]);
							}
						}
					}					
				}else{
					if($data.javascript!=undefined){
						for(var $js in $data.javascript){
							jQuery('body').append($data.javascript[$js]);
						}
					}
				}
			}else{
				/*error*/
			}
		},
		_setLoading:function($options){},
		_unsetLoading:function($options){}
	};

	this.init();
}
JFuncs.Jax.prototype = {
	init:function(){
		var $self = this;
		jQuery(function(){});
	},
	getid:function(){
		this.id += 1;
		return this.id;
	},
	is_ajax_supported:function(){
		var $self;
		$self = this;		
		return $self.enable_ajax==1 && (window.XMLHttpRequest || window.ActiveXObject);
	},
	cancel:function($id){
		if(this.xhrs[$id]){
			this.xhrs[$id].cancel();
			this.xhrs[$id] = null;
		}
	},
	link:function($url,$overlay,$container){
		if($url==undefined){
			return false;
		}		
		var $self;
		$self = this;
		$self.ajax({
			_action:$url,
			_obj:$container,
			_setLoading:function($options){
				if($overlay){
					var $t1;
					$t1 = jQuery('<div class="'+$self.loading_mask_class+'" style="position:fixed;top:0;left:0;width:100%;height:100%;background:transparent;"><table width="100%" height="100%"><tr><td align="center" valign="center"><img src="'+$self.loading_img+'"></img></td></tr></table></div>');
					$t1.appendTo('body');
				}
			},
			_unsetLoading:function($options){
				if($overlay){
					jQuery('.'+$self.loading_mask_class).remove();
				}
			}
		});
		return false;
	},
	submit:function($obj){
		if($obj==undefined){
			return false;
		}
		if(typeof $obj === 'string'){
			$obj = jQuery('#'+$obj);
		}else{
			$obj = jQuery($obj);
		}
		if($obj.length==0){
			return false;
		}
		var $self;
		$self = this;
		$self.ajax({
			_form_submit:1,
			_formid:$obj.closest('form'),
			_fired:$obj,			
			_setLoading:function($options){
				var $t1;
				$t1 = jQuery('<div class="'+$self.loading_mask_class+'" style="position:fixed;top:0;left:0;width:100%;height:100%;background:transparent;"><table width="100%" height="100%"><tr><td align="center" valign="center"><img src="'+$self.loading_img+'"></img></td></tr></table></div>');
				$t1.appendTo('body');
			},
			_unsetLoading:function($options){
				jQuery('.'+$self.loading_mask_class).remove();
			}
		});
		return false;
	},
	dialog:function($action,$popupid){
		if($action==undefined || JFunc.Popup==undefined){
			return false;
		}
		var $self,$id;
		$self = this;
		if($popupid==undefined){
			$id = $self.getid();
			$popupid = JFunc.Popup.open({'prog':'Jax','id':$id},true);
		}
		$self.ajax({			
			_obj:$popupid,
			_action:$action,
			_error:function($obj, $textStatus, $errorThrown){
				if($obj._obj!=undefined && JFunc.Popup!=undefined && document.getElementById($obj._obj)!=null){
					JFunc.Popup.show($obj._obj,$textStatus,$errorThrown);
				}
			},
			_return:function($obj,$status,$data){				
				JFunc.Popup.show($obj._obj,$status,$data);
			},
			_setLoading:function($obj){
				if($obj._obj!=undefined && JFunc.Popup!=undefined && document.getElementById($obj._obj)!=null){
					var $element = jQuery('#'+$obj._obj);
					JFunc.Popup.setLoading($element);
				}
			}
		});
		return false;
	},	
	popup:function($action,$popupid){
		if($action==undefined || JFunc.Popup==undefined){
			return false;
		}
		var $self,$id;
		$self = this;
		if($popupid==undefined){
			$id = $self.getid();
			$popupid = JFunc.Popup.open({'prog':'Jax','id':$id},false);
		}
		$self.ajax({			
			_obj:$popupid,
			_action:$action,
			_error:function($obj, $textStatus, $errorThrown){
				if($obj._obj!=undefined && JFunc.Popup!=undefined && document.getElementById($obj._obj)!=null){
					JFunc.Popup.show($obj._obj,$textStatus,$errorThrown);
				}
			},
			_return:function($obj,$status,$data){				
				JFunc.Popup.show($obj._obj,$status,$data);
			},
			_setLoading:function($obj){				
				if($obj._obj!=undefined && JFunc.Popup!=undefined && document.getElementById($obj._obj)!=null){
					var $element = jQuery('#'+$obj._obj);
					JFunc.Popup.setLoading($element);
				}
			}
		});
		return false;
	},		
	ajax:function($o){
		var $self = this;		
		$self._options._id = $self.getid();
		$o = jQuery.extend({},$self._options, $o);
		if($self.is_ajax_supported() && $o._form_submit==0){
			this.xhrs[$self._options._id] = new JFuncs.Jax['AjaxHandler']($o);
		}else{			
			this.xhrs[$self._options._id] = new JFuncs.Jax['FormHandler']($o);
		}
		this.xhrs[$self._options._id].exec();
		
		return $self._options._id;
	}
}
jQuery(function(){
	JFunc.run('Jax');
});

/*
 * Sub class
 *
 */

JFuncs.Jax.AjaxHandler = function($o){
	this.xhr = null;
	this._options = {
		_id:null,
		_data:{},
		_formid:null,
		_fileid:null,
		_fired:null,
		_obj:null,
		_action:null,		
		_type:"post",
		_onProgress:function($options, $loaded, $total){},
		_error:function($options, $textStatus, $errorThrown){},
		_return:function($options,$status,$data){},
		_setLoading:function($options){},
		_unsetLoading:function($options){}
	};
	this._options = jQuery.extend({},this._options, $o);
}
JFuncs.Jax.AjaxHandler.prototype = {
	cancel:function(){
		var $self;
		$self = this;
		if($self.xhr!=null){
			$self._options._unsetLoading($self._options);
			$self.xhr.abort();
			$self.xhr = null;
		}
	},
	exec:function(){
		var $self,$data;
		$self = this;		
		$self._options._setLoading($self._options);
		$data = $self.getFormData();
		$self.xhr = jQuery.ajax({
			url: $self._options._action,
			data: $data,
			type: $self._options._type,			
			error:function($jqXHR, $textStatus, $errorThrown){
				$self._options._error($self._options, $textStatus, $errorThrown);
			},
			complete:function($jqXHR, $textStatus){
				if($textStatus=="success" && $jqXHR.responseText!=undefined && $jqXHR.responseText.length>0){
					var $data = $jqXHR.responseText;
					try{
						$data = eval("(" + $data + ")");
					}catch(e){
						$data = {};
					}
					$self._options._return($self._options,$textStatus,$data);
				}else{
					$self._options._return($self._options,$textStatus,"");
				}								
				JFunc.Jax.cancel($self._options._id);
			}
		});
	},
	upload: function(){
		var $self,$file,$name,$size,$queryString;
		$self = this;
		$name = $self.getName();
		$size = $self.getSize();		
		if($size===0){
			JFunc.Jax.cancel($self._options._id);
			return;
		}
		$file = jQuery('#'+$self._options._fileid);
		$self.xhr = new XMLHttpRequest();
		$self._options._setLoading($self._options);                
        $self.xhr.upload.onprogress = function(e){
            if (e.lengthComputable){
                $self._options._onProgress($self._options, e.loaded, e.total);
            }
        };
        $self.xhr.onreadystatechange = function(){
            // the request was aborted/cancelled
            if (!$self.xhr){
                return;
            }
            if ($self.xhr.readyState == 4){
				$self._options._unsetLoading($self._options);
                if ($self.xhr.status == 200){
					var $data = $self.xhr.responseText;
					try{
						$data = eval("(" + $data + ")");
					}catch(e){
						$data = {};
					}
					$self._options._return($self._options,"success",$data);
                }else{
					$self._options._error($self._options, "fail", "");
				}
				JFunc.Jax.cancel($self._options._id);
            }
        };        
        $queryString = '?' + $file.attr('name') + '=' + encodeURIComponent($name);
        $self.xhr.open("POST", this._options._action + $queryString, true);
        $self.xhr.send($file[0]);
    },
	getFormData:function() {
		var $self,$form,$data,$i;
		$self = this;
		$data = $self._options._data;
		if(typeof $self._options._formid === 'string'){
			$form = jQuery('#'+$self._options._formid);
		}else{
			$form = jQuery($self._options._formid);
		}		
		if($form.length){
			$form = $form.serializeArray();
			for ($i in $form) {
				$data[$form[$i].name] = $form[$i].value;
			}
		}
		return $data;
	},
	getName: function(){
		var $self,$file;
		$self = this;
        // fix missing name in Safari 4		
        $file = jQuery('#'+$self._options._fileid);
		if($file.length==0){
			return false;
		}
		if($file.jQuery){
			$file = $file[0];
		}
        return $file.fileName != null ? $file.fileName : $file.name;
    },
	getSize: function(){
		var $self,$file,$size;
		$self = this;
        // fix missing size in Safari 4
        $file = jQuery('#'+$self._options._fileid);
		if($file.length==0){
			return false;
		}
		if($file.jQuery){
			$file = $file[0];
		}
		$size = $file.fileSize != null ? $file.fileSize : $file.size;
		if ($size === 0){
			return false;
		}		
        return $size;
    }
}

JFuncs.Jax.FormHandler = function($o){
	this.iframe = null;	
	this._options = {
		_id:null,
		_data:{},
		_formid:null,
		_fileid:null,
		_fired:null,
		_obj:null,
		_action:null,
		_type:"post",
		_error:function($options, $textStatus, $errorThrown){},
		_return:function($options,$status,$data){},
		_setLoading:function($options){},
		_unsetLoading:function($options){}
	};
	this._options = jQuery.extend({},this._options, $o);
}
JFuncs.Jax.FormHandler.prototype = {
	cancel:function(){
		var $self;
		$self = this;		
		if($self.iframe!=null){			
			$self._options._unsetLoading($self._options);
			$self.iframe.attr('src', 'javascript:false;');
            $self.iframe.remove();
			$self.iframe = null;
		}
	},
	exec:function(){
		var $self,$form;
		$self = this;				
		$self._options._setLoading($self._options);
		$self.iframe = $self._createIframe();
		$form = $self._createForm();
		$self.iframe
		.load(function(){
			var $data = $self.iframe.contents().find('body').html();
			try{
				$data = eval("(" + $data + ")");
			}catch(e){
				$data = {};
			}
			$self._options._return($self._options,"success",$data);
			JFunc.Jax.cancel($self._options._id);
		})
		.error(function(){
			$self._options._error($self._options, "Fail", "");			
		});
        $form.submit();
		$form.remove();
	},
	_createIframe: function(){
		var $self,$id,$iframe;
		$self = this;
		$id = "Jax_iframe_"+$self._options._id;
        $iframe = jQuery('<iframe src="javascript:false;" />');
		$iframe.attr('name',$id);
        $iframe.attr('id', $id);
        $iframe.css('display','none');
		$iframe.appendTo('body');

        return $iframe;
    },
	_createForm: function(){
		var $self,$form,$file,$data;
		$self = this;

		if(typeof $self._options._formid === 'string'){
			$form = jQuery('#'+$self._options._formid);
		}else{
			$form = jQuery($self._options._formid);
		}
		if($form.length){
			$form = $form.clone(true);
			if($form.attr('action')!=null){
				$self._options._action = $form.attr('action');				
			}
			if($form.attr('method')!=null){
				$self._options._type = $form.attr('method');
			}
		}else{
			$form = jQuery('<form></form>');
		}
		$form.attr('method', $self._options._type);
		$form.attr('enctype', "multipart/form-data");
		$form.attr('action', $self._options._action);
		$form.attr('target', $self.iframe.attr('name'));
		$form.css('display','none');
		$form.appendTo('body');

		$file = jQuery('#'+$self._options._fileid);
		if($file.length){
			$file.clone(true).children().andSelf().unbind().appendTo($form);
		}

		jQuery.each($self._options._data,function($key,$value){
			jQuery('<input type="hidden" name="'+$key+'" value="'+$value+'">').appendTo($form);
		});

        return $form;
    }
}

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
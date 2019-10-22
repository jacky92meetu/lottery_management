if(JFuncs==undefined){
	var JFuncs = {};
}

/*
 * JUpload
 *
 */
JFuncs.JUpload = function(){
	this.id = 0;
	this.enable_ajax = 1;
	this._options = {
		allowedExtensions: [],
		sizeLimit: (1024*1024),
		action: null,
		button_id: "jupload_button",
		container_id: "jupload_container",
		input_id: "file",
		complete: function($option,$input_id,$data){
			if($data.javascript!=undefined){					
				for(var $js in $data.javascript){
					jQuery('body').append($data.javascript[$js]);
				}
			}
		},
		setLoading:function($option,$input_id){},
		unsetLoading:function($option,$input_id){}
	}

	this.init();
}
JFuncs.JUpload.prototype = {
	init: function(){		
		jQuery(function(){
			
		});
	},
	is_ajax_supported:function(){
		var $self;
		$self = this;		
		return $self.enable_ajax==1 && (window.XMLHttpRequest || window.ActiveXObject);
	},
	start: function($o){		
		var $self,$obj;
		$self = this;
		jQuery.extend($self._options,$o);
		jQuery('#'+$self._options.button_id).each(function(){
			$obj = jQuery(this);
			$obj.css({'position':'relative','overflow':'hidden'});
			$self._createInput($obj);
		});
	},
	get_id: function(){
		var $self;
		$self = this;
		$self.id = $self.id + 1;
		return $self.id;
	},	
	_createInput: function($element){
		var $self = this;
        var $input = jQuery('<input type="file">');
        $input.attr("name", $self._options.input_id);
        $input.css({
            position: 'absolute',            
            right: 0,
            top: 0,
            zIndex: 1,
            fontSize: '460px',
            margin: 0,
            padding: 0,
            cursor: 'pointer',
            opacity: 0
        });
		$input.change(function(){			
            $self._check(this);
        });		
		$input.appendTo(jQuery($element));
    },	
	_createDummyInput: function(){
		var $self = this;
        var $input = jQuery('<input type="file">');
//		$input.attr("multiple","multiple");
        $input.attr("name", $self._options.input_id);        
		return $input;
    },
	_check: function($input){
		var $self,$valid,$i,$files,$obj;
		$self = this;		
		$valid = 1;		
		$files = $input.files;
		$obj = jQuery($input).closest('#jupload_button');
		if(!$files.length){
			return false;
		}		
		for($i=0; $i<$files.length; $i++){
			if(!$self._validateFile($files[$i])){
				$valid = 0;
			}
		}
		if($valid){			
			$self._upload(jQuery($input).clone());
			//$self._createInput($obj);
		}			
		return true;
	},
	_isAllowedExtension: function(fileName){
		var $self;
		$self = this;
        var ext = (-1 !== fileName.indexOf('.')) ? fileName.replace(/.*[.]/, '').toLowerCase() : '';
        var allowed = $self._options.allowedExtensions;        
        if (!allowed.length){return true;}                
        for (var i=0; i<allowed.length; i++){
            if (allowed[i].toLowerCase() == ext){
                return true;
            }    
        }        
        return false;
    },
	_validateFile: function(file){
        var $self,name,size; 
		$self = this;
        if (file.value){
            // it is a file input            
            // get input value and remove path to normalize
            name = file.value.replace(/.*(\/|\\)/, "");
        } else {
            // fix missing properties in Safari
            name = file.fileName != null ? file.fileName : file.name;
            size = file.fileSize != null ? file.fileSize : file.size;
        }                    
        if (! $self._isAllowedExtension(name)){
            return false;            
        } else if (size === 0){
            return false;                                                     
        } else if (size && $self._options.sizeLimit && size > $self._options.sizeLimit){
            return false;            
        }        
        return true;                
    },
	_upload: function($input){
        var $self,$id;
		$self = this;		
		$id = $self.get_id();
		$self._options.setLoading($self._options,$id);
		var $iframe = $self._createIframe($id);		
		$iframe.attr('input_id',$id);
		var $form = $self._createForm($iframe);
		jQuery($input).appendTo($form);	
		$iframe.load(function(){			
			$self._options.unsetLoading($self._options,$iframe.attr('input_id'));
			var $data = $iframe.contents().find('body').html();
			$self._options.complete($self._options,$iframe.attr('input_id'),eval('(' + $data + ')'));
			$self._cancel($iframe);
		});
		$form.submit();
		$form.remove();
    },
	_cancel: function($iframe){
		var $self;
		$self = this;

        if ($iframe.length){
            $iframe.attr('src', 'javascript:false;');
            $iframe.remove();
        }
    },
	_createIframe: function($id){
		var $temp;
		$temp = "jupload-iframe-"+$id;
        var $iframe = jQuery('<iframe src="javascript:false;" />');
		$iframe.attr('name',$temp);
        $iframe.attr('id', $temp);
        $iframe.css('display','none');
		$iframe.appendTo('body');
		
        return $iframe;
    },
	_createForm: function($iframe){
        var $form = jQuery('<form method="post" enctype="multipart/form-data"></form>');
        $form.attr('action', this._options.action);
        $form.attr('target', $iframe.attr('name'));
        $form.css('display','none');		
		$form.appendTo('body');

        return $form;
    }
}
jQuery(function(){
	JFunc.run('JUpload');
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
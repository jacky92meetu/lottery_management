if(JFuncs==undefined){
	var JFuncs = {};
}

/*
 * Country
 * Example:
 
  jQuery(function(){
	JFunc.JCountry.start({
		selected_country:"{g_country}",
		selected_state:"{g_state}",
		selected_city:"{g_city}"
	});
  });
  
  <span class="country" id="country_container"></span>
  <span class="country" id="state_container"></span>
  <span class="country" id="city_container"></span>
  
 * include myScript_Jax.js
 */
JFuncs.JCountry = function(){
	this._options = {
		selected_country: "",
		selected_state: "",
		selected_city: "",
		country_oname: "country",
		state_oname: "state",
		city_oname: "city",
		action: "country.php",
		allow_empty:0
	}
	
	this.init();
}
JFuncs.JCountry.prototype = {
	init:function(){
		var $self = this;
		jQuery(function(){
			var $temp = jQuery('<div class="country_bin" style="display:none;"></div>');
			$temp.appendTo('body');
		});
	},
	start:function($o){
		var $self = this;
		$self._options = jQuery.extend($self._options, $o);
		$self.country_change();
	},
	country_change:function(){
		var $self = this;        
		jQuery('#'+$self._options.state_oname+'_container select').appendTo('div.country_bin');
		jQuery('#'+$self._options.city_oname+'_container select').appendTo('div.country_bin');

		JFunc.Jax.ajax({
			_data:{
				ctype:1
			},
			_fired:null,
			_type:"post",
			_obj:jQuery('#'+$self._options.country_oname+'_container'),
			_action:$self._options.action,
			_return:function($obj,$status,$data){
				$data = eval('('+$data+')');
				var $content = jQuery('<SELECT class="selected" name="'+$self._options.country_oname+'" id="'+$self._options.country_oname+'"></SELECT>');
				if($self._options.allow_empty){
					$content.append(jQuery('<OPTION value="" SELECTED></OPTION>'));
				}
				for(var $i in $data.data){
                    if($data.data[$i].id!=undefined){
                        if($data.data[$i].id==$self._options.selected_country){
                            $content.append(jQuery('<OPTION value="'+$data.data[$i].id+'" name="'+$data.data[$i].name+'" SELECTED>'+$data.data[$i].name+'</OPTION>'));
                        }else{
                            $content.append(jQuery('<OPTION value="'+$data.data[$i].id+'" name="'+$data.data[$i].name+'">'+$data.data[$i].name+'</OPTION>'));
                        }
                    }
				}
				$content.change(function(){
					$self.state_change();
				});
				$obj._obj.append($content);
				$self._options.selected_country = null;
				$self.state_change();
			}
		});
	},
	state_change:function(){
		var $self = this;
		jQuery('#'+$self._options.state_oname+'_container select').appendTo('div.country_bin');
        jQuery('#'+$self._options.city_oname+'_container select').appendTo('div.country_bin');
		var $country = jQuery('#'+$self._options.country_oname+'_container select.selected');		
		var $selected_obj = jQuery('div.country_bin select.'+$self._options.state_oname+'_'+$country.find('option:selected').val());		
		if($selected_obj.length){
			$selected_obj.appendTo('#'+$self._options.state_oname+'_container');
			$self.city_change();
			return;
		}
		JFunc.Jax.ajax({
			_data:{
				ctype:2,
				country:$country.find('option:selected').val()
			},
			_fired:null,
			_type:"post",
			_obj:jQuery('#'+$self._options.state_oname+'_container'),
			_action:$self._options.action,
			_return:function($obj,$status,$data){
				$data = eval('('+$data+')');
				var $content = jQuery('<SELECT class="selected '+$self._options.state_oname+'_'+$country.find('option:selected').val()+'" name="'+$self._options.state_oname+'" id="'+$self._options.state_oname+'"></SELECT>');
				if($self._options.allow_empty){
					$content.append(jQuery('<OPTION value="" SELECTED></OPTION>'));
				}
				for(var $i in $data.data){
                    if($data.data[$i].id!=undefined){
                        if($data.data[$i].id==$self._options.selected_state){
                            $content.append(jQuery('<OPTION value="'+$data.data[$i].id+'" name="'+$data.data[$i].name+'" SELECTED>'+$data.data[$i].name+'</OPTION>'));
                        }else{
                            $content.append(jQuery('<OPTION value="'+$data.data[$i].id+'" name="'+$data.data[$i].name+'">'+$data.data[$i].name+'</OPTION>'));
                        }
                    }
				}
				$content.change(function(){
					$self.city_change();
				});
				$obj._obj.append($content);
				$self._options.selected_state = null;
				$self.city_change();
			}
		});
	},
	city_change:function(){
		var $self = this;
		jQuery('#'+$self._options.city_oname+'_container select').appendTo('div.country_bin');
		var $country = jQuery('#'+$self._options.country_oname+'_container select.selected');
		var $state = jQuery('#'+$self._options.state_oname+'_container select.selected');		
		var $selected_obj = jQuery('div.country_bin select.'+$self._options.city_oname+'_'+$country.find('option:selected').val()+'-'+$state.find('option:selected').val());		
		if($selected_obj.length){			
			$selected_obj.appendTo('#'+$self._options.city_oname+'_container');
			return;
		}
		JFunc.Jax.ajax({
			_data:{
				ctype:3,
				country:$country.find('option:selected').val(),
				state:$state.find('option:selected').val()
			},
			_fired:null,
			_type:"post",
			_obj:jQuery('#'+$self._options.city_oname+'_container'),
			_action:$self._options.action,
			_return:function($obj,$status,$data){
				$data = eval('('+$data+')');
				var $content = jQuery('<SELECT class="selected '+$self._options.city_oname+'_'+$country.find('option:selected').val()+'-'+$state.find('option:selected').val()+'" name="'+$self._options.city_oname+'" id="'+$self._options.city_oname+'"></SELECT>');
				if($self._options.allow_empty){
					$content.append(jQuery('<OPTION value="" SELECTED></OPTION>'));
				}
				for(var $i in $data.data){
                    if($data.data[$i].id!=undefined){
                        if($data.data[$i].id==$self._options.selected_city){
                            $content.append(jQuery('<OPTION value="'+$data.data[$i].id+'" name="'+$data.data[$i].name+'" SELECTED>'+$data.data[$i].name+'</OPTION>'));
                        }else{
                            $content.append(jQuery('<OPTION value="'+$data.data[$i].id+'" name="'+$data.data[$i].name+'">'+$data.data[$i].name+'</OPTION>'));
                        }
                    }
				}
				$obj._obj.append($content);
				$self._options.selected_city = null;
			}
		});
	}
}
jQuery(function(){
	JFunc.run('JCountry');
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
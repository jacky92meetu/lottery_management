if(JFuncs==undefined){
	var JFuncs = {};
}

/*
 * JScript
 *
 */
JFuncs.JScript = function(){
	this.init();
}
JFuncs.JScript.prototype = {
	init: function(){
		jQuery(function(){
			JFunc.JScript.exec();
		});
	},
	exec: function($content){
		if($content==undefined){
			$content = jQuery('body');
		}else{
			$content = jQuery($content);
		}
		$content.find('.javascript').each(function(){
			eval(jQuery(this).html());
		}).remove();
	}
}
jQuery(function(){
	JFunc.run('JScript');
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
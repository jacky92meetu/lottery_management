<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Lang extends CI_Lang {
	
	var $image_list = null;
	
	function __construct()
	{	
		$this->_get_image_list();
		parent::__construct();
	}

	function line($line = '')
	{		
		$data = func_get_args();
		array_shift($data);		
		$line = (isset($this->language[$line])) ? $this->language[$line] : $line;
		$subject = array();
		$replacement = array();
		foreach($data as $key => $value){
			$subject[] = "/\\$".$key."/i";
			$replacement[] = $value;
		}
		$line = preg_replace($subject,$replacement,$line);

		return $line;
	}
	
	function image($name=""){
		$name = strtolower($name);
		if(array_key_exists($name, $this->image_list)){
			return $this->image_list[$name];
		}
		return false;
	}
	
	function _get_image_list(){		
		$path = APPPATH."globals/images/system/";		
		foreach(array('ico','small') as $value){
			$dir = $path.$value;
			if(is_dir($dir)){
				$files = scandir($dir);
				if(sizeof($files)>0){
					foreach($files as $file){
						$name = "";
						preg_match('#(.*)\.[\w\d]+$#i', $file, $matches);
						if(isset($matches[1])){
							$name = $matches[1];
							if(strlen($name)>0){
								$this->image_list[strtolower($name)] = "/".$dir."/".$file;
							}
						}
					}
				}
			}
		}
	}

}
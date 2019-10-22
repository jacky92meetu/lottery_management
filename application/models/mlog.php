<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mlog extends CI_Model {

    function __construct()
    {
        parent::__construct();		
    }
	
	function add_log($data = array()){
		$date = date("Y-m-d H:i:s");
		$query = "INSERT INTO submission_log(`cip`,`ctype`,`cremarks`,`ctext`,`cdate`) 
					VALUES('".$data['cip']."','".$data['ctype']."','".$data['cremarks']."','".$data['ctext']."','".$date."');";
		return $this->cmysqli->run($query);
	}
    
}

<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Madmin extends CI_Model {

    function __construct()
    {
        parent::__construct();		
    }
		
	function get_allow_dns(){
		$query = 'SELECT * FROM allow_dns';
		return $this->cmysqli->result($query);
	}
}

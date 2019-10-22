<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mpagination extends CI_Model {

    function __construct()
    {
        parent::__construct();		
    }	
		    
    function get_count($query,$db_group_name = ''){		
		$query = "SELECT COUNT(*) counting FROM (".$query.") a";
		$result = null;
		$db = $this->load->database($db_group_name,TRUE);
		if($db){
			$result = $db->query($query);
			$db->close();			
			if($result){
				if(method_exists($result, "num_rows") && $result->num_rows()){
					$temp = $result->result_array();
					$result->free_result();
					if(sizeof($temp)>0){
						return $temp;
					}					
				}
			}
		}			
		return false;		
	}    
}

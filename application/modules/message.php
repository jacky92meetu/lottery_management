<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	$CI =& get_instance();
	$div = '
		<DIV class="response_message"><DIV class="{class}">{message}</DIV></DIV>
	';	
    $CI->load->library('cmessage');
	
	$temp = $CI->cmessage->get_response_message();
	$CI->cmessage->del_response_message();
	if(empty($temp) || sizeof($temp)==0){
		return false;
	}
	
	$contents = "";
	foreach($temp as $value){
		$text = "";
		switch($value['type']){
			case "warning":	
					$text = str_ireplace("{class}", "message_warning", $div);
					break;

			case "error":
					$text = str_ireplace("{class}", "message_error", $div);
					break;

			case "notice":
			default:
					$text = str_ireplace("{class}", "message_notice", $div);
					break;
		}	
		$contents .= str_ireplace("{message}", $value['message'], $text);
	}		
	
	echo $contents;	
?>

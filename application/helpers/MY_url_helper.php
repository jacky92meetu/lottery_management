<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function redirect($uri = '', $method = 'location', $http_response_code = 302)
{
//	if ( ! preg_match('#^https?://#i', $uri))
//	{
//		$uri = site_url($uri);
//	}

	switch($method)
	{
		case 'refresh'	: header("Refresh:0;url=".$uri);
			break;
		default			: header("Location: ".$uri, TRUE, $http_response_code);
			break;
	}
	exit;
}

?>
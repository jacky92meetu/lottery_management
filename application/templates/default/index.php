<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
	header('Content-Type: text/html; charset=UTF-8');
?>
<html>
	<head>
		<module type="head" />
		<link rel="stylesheet" href="/application/templates/default/css/main.css" type="text/css" />
		<link rel="stylesheet" href="/application/templates/default/css/admin.css" type="text/css" />
		<link href="/application/templates/default/images/favicon.png" rel="icon" />
	</head>	
	
<body>		
	
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
<tr>
	<td valign="top">
		<div id="contents">
			<module type="header" />
			<table width="100%" height="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td valign="top" style="padding:5px;">
					<module type="message" />
					<module type="title" />					
					<table width="100%" height="100%" cellpadding="0" cellspacing="0">
					<tr><td valign="top" style="padding:10px 0;"><module type="contents" /></td></tr>
					</table>							
				</td>
			</tr>
			</table>			
		</div>			
		<module type="footer" />
	</td>
</tr>
</table>	
	
</body>
</html>

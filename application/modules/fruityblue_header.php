<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	global $RTR;
	$method = $RTR->fetch_class();
	$CI =& get_instance();
	$user =& $CI->cuser->getLoginUser();
?>
<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td align="left" rowspan="2">
		<div id="logo">
			<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td align="center" valign="center">test</td>
				</tr>
			</table>
		</div>
	</td>
	<td align="right" valign="top">
		<div style="float:right;padding:20px;">
			<?php if($user->id!=0){ ?>
				Welcome, <?php echo $user->display_name; ?>&nbsp;|&nbsp;<a href="/member/logout">Log Out</a>
			<?php }else{ ?>
				<a href="/member/login">Login</a>&nbsp;|&nbsp;<a href="/member/register">Join Now</a>
			<?php } ?>
		</div>
	</td>
</tr>
<tr>
	<td align="right" valign="bottom">
		<div id="menu">
			<ul>
				<li class="<?php echo (($method=='home')?"current_page_item":""); ?>"><a href="/home">Homepage</a></li>							
				<li class="<?php echo (($method=='member')?"current_page_item":""); ?>"><a href="/member">Member Area</a></li>
				<li class="<?php echo (($method=='product')?"current_page_item":""); ?>"><a href="/product">Products</a></li>
				<li class="<?php echo (($method=='contact')?"current_page_item":""); ?>"><a href="/contact">Contact</a></li>										
			</ul>
		</div>
	</td>
</tr>
</table>
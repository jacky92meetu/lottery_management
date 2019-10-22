<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	$CI =& get_instance();
	global $RTR;
	$class = $RTR->fetch_class();
	$CI->load->library('cuser');
	if(!$CI->cuser->is_login() || $class!='member'){
		return false;
	}	
	
	$CI->cpage->set_stylesheet("menu.css");	
	$CI->cpage->set_stylesheet("ddsmoothmenu.css");	
	$CI->cpage->set_javascript("ddsmoothmenu.js");	
?>

<script>    
    jQuery(function(){
        jQuery('li[ico]').each(function(){
            var $o = jQuery(this);
            var $v = "/application/globals/images/system/ico/"+$o.attr('ico');
            $o.attr('ico',$v);
        });
        ddsmoothmenu.init({
            mainmenuid: "smoothmenu1", //menu DIV id
            orientation: 'h',
            classname: 'ddsmoothmenu'
        });
    });
	
</script>

<div id="smoothmenu1" class="ddsmoothmenu" style="border:none;">
    <ul>        
        <li>            
            <a href="javascript:void(0)"><div>System</div></a>
            <ul>
				<li ico="ico_profile.jpg"><a href="/member/profile"><div>Profile</div></a></li>
				<li ico="ico_password.jpg"><a href="/member/change_pwd"><div>Change Password</div></a></li>
                <li ico="ico_log_out.jpg"><a href="/member/logout"><div>Log out</div></a></li>
            </ul>
        </li>		 
		<li>
            <a href="javascript:void(0)"><div>Tools & Managements</div></a>
            <ul>				
				<li ico="ico_list.jpg"><a href="/member/subscribe_list"><div>Package Subscription List</div></a></li>
				<li ico="ico_buy.jpg"><a href="/product"><div>Buy Package</div></a></li>
			</ul>
        </li>
		<li>
            <a href="javascript:void(0)"><div>Reports</div></a>
            <ul>
				<li ico="ico_transaction.jpg"><a href="/member/transaction_list"><div>Transaction List</div></a></li>				
			</ul>
        </li>
    </ul>    
    
    <br style="clear: both;" />
</div>
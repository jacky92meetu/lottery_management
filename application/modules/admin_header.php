<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	$CI =& get_instance();
	$CI->cpage->set_stylesheet("menu.css");
	$CI->load->library("cadmin");
	if(!$CI->cadmin->is_login()){
		return;
	}
	$admin = $CI->cadmin->getLoginUser();
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

<div style="color:#ffffff;background:#3dc1e8;height:50px;">
	<div style="padding:0 10px;float:left;font-size:36px;font-weight:bold;">
		Admin Control Panel
	</div>	
	<div style="padding:0 10px;float:right;">
		Welcome, <?php echo $admin->display_name; ?>
	</div>	
</div>

<div id="smoothmenu1" class="ddsmoothmenu">
    <ul>        
        <li>            
            <a href="javascript:void(0)"><div>Sites</div></a>
            <ul>
                <li ico="ico_home.jpg"><a href="/admin/index"><div>Control Panel</div></a></li>                
                <li ico="ico_log_out.jpg"><a href="/admin/logout"><div>Log out</div></a></li>        
            </ul>
        </li>		
		<li>            
            <a href="javascript:void(0)"><div>Sales Management</div></a>
            <ul>
                <li ico="ico_basket.jpg"><a href="/admin/page/tran/tran_order_list_view"><div>Order Transaction</div></a></li>
				<li ico="ico_add.jpg"><a href="/admin/page/tran/add_tran_order"><div>Add New Order</div></a></li>
				<li ico="ico_refund.jpg"><a href="/admin/page/tran/tran_order_refundable_list_view"><div>Refundable Transaction</div></a></li>
				<li ico="ico_site_setting.jpg">
					<a href="javascript:void(0)"><div>Transaction Settings</div></a>
					<ul>						
						<li ico="ico_site_setting.jpg"><a href="/admin/page/tran/tran_product_code_management_list_view"><div>Product Code</div></a></li>
					</ul>
				</li>
            </ul>
        </li>
		<li>
            <a href="javascript:void(0)"><div>Tools & Managements</div></a>
            <ul>                
                <li ico="ico_user_manager.jpg">
					<a href="javascript:void(0)"><div>User Manager</div></a>
					<ul>                
						<li ico="ico_list.jpg"><a href="/admin/page/user/list_view"><div>User List</div></a></li>
						<li ico="ico_list.jpg"><a href="/admin/page/user/user_points_list"><div>User Points List</div></a></li>
						<li ico="ico_add.jpg"><a href="/admin/page/user/add"><div>New User</div></a></li>
						<li ico="ico_user_group.jpg"><a href="/admin/page/user_group/list_view"><div>User Group</div></a></li>
					</ul>
				</li>
				<li ico="ico_phone.jpg">
					<a href="javascript:void(0)"><div>Phone Manager</div></a>
					<ul>                
						<li ico="ico_list.jpg"><a href="/admin/page/phone/list_view"><div>Phone List</div></a></li>
						<li ico="ico_add.jpg"><a href="/admin/page/phone/add"><div>New Phone</div></a></li>
						<li ico="ico_add.jpg"><a href="/admin/page/phone/add_by_points"><div>New Subscription by Points</div></a></li>
					</ul>
				</li>
				<li ico="ico_lottery.jpg">
					<a href="javascript:void(0)"><div>Lottery Tools</div></a>
					<ul>         
						<li ico="ico_add.jpg">
							<a href="javascript:void(0)"><div>New Result</div></a>
							<ul>
								<li ico="ico_add.jpg"><a href="/admin/page/lottery/lottery_add_menu"><div>New Lottery Result</div></a></li>								
							</ul>
						</li>
						<li ico="ico_sms.jpg">
							<a href="javascript:void(0)"><div>Result SMS Send</div></a>
							<ul>         
								<li ico="ico_sms.jpg"><a href="/admin/page/lottery_sms/sms_send"><div>Send one SMS</div></a></li>
								<li ico="ico_sms.jpg"><a href="/admin/page/lottery_sms/sms_bulk_send"><div>Send bulk SMS</div></a></li>
							</ul>
						</li>
					</ul>
				</li>
				<li ico="ico_sms.jpg">
					<a href="javascript:void(0)"><div>SMS</div></a>
					<ul>                
						<li ico="ico_sms.jpg"><a href="/admin/page/lottery_sms/sms_msg_send"><div>Send to one</div></a></li>
						<li ico="ico_sms.jpg"><a href="/admin/page/lottery_sms/sms_msg_all_user_send"><div>Send to all user</div></a></li>
						<li ico="ico_sms.jpg"><a href="/admin/page/lottery_sms/sms_msg_type_bulk_send"><div>Send to active subscriber</div></a></li>
					</ul>
				</li>
            </ul>
        </li>
		<li>            
            <a href="javascript:void(0)"><div>System Reports</div></a>
            <ul>
				<li ico="ico_lottery.jpg">
					<a href="javascript:void(0)"><div>Lottery Summary</div></a>
					<ul>
						<li ico="ico_list2.jpg"><a href="/admin/page/lottery/lottery_result_by_date"><div>Lottery Result By Date</div></a></li>
						<li ico="ico_list.jpg"><a href="/admin/page/lottery/summary_view"><div>Lottery Summary</div></a></li>
						<li ico="ico_list.jpg"><a href="/admin/page/lottery/summary_result/3d"><div>3D Result Summary</div></a></li>
						<li ico="ico_list.jpg"><a href="/admin/page/lottery/summary_result/4d"><div>4D Result Summary</div></a></li>
						<li ico="ico_list.jpg"><a href="/admin/page/lottery/summary_result/5d"><div>5D Result Summary</div></a></li>
						<li ico="ico_list.jpg"><a href="/admin/page/lottery/summary_result/6d"><div>6D Result Summary</div></a></li>
						<li ico="ico_list.jpg"><a href="/admin/page/lottery/summary_result/jackpot"><div>JACKPOT Result Summary</div></a></li>
					</ul>
				</li>
				<li ico="ico_list.jpg">
					<a href="javascript:void(0)"><div>Lottery Product</div></a>
					<ul>                
						<li ico="ico_list.jpg"><a href="/admin/page/lottery/product_summary_view"><div>Product View</div></a></li>						
					</ul>
				</li>
				<li ico="ico_list.jpg">
					<a href="javascript:void(0)"><div>Lottery Subscriber</div></a>
					<ul>                
						<li ico="ico_list.jpg"><a href="/admin/page/lottery/msubscribe_summary_view"><div>Subscriber Summary</div></a></li>
						<li ico="ico_list.jpg"><a href="/admin/page/lottery/msubscribe_group_view"><div>Subscriber Group</div></a></li>
						<li ico="ico_list.jpg"><a href="/admin/page/lottery/msubscribe_user_summary_view"><div>Subscription User Summary</div></a></li>						
					</ul>
				</li>
                <li ico="ico_list.jpg">
					<a href="javascript:void(0)"><div>SMS Summary</div></a>
					<ul>                
						<li ico="ico_list.jpg"><a href="/admin/page/lottery_sms/sms_inbox_report"><div>SMS Inbox</div></a></li>
						<li ico="ico_list.jpg"><a href="/admin/page/lottery_sms/sms_outbox_report"><div>SMS Outbox</div></a></li>						
					</ul>
				</li>
				<li ico="ico_list.jpg">
					<a href="javascript:void(0)"><div>Guest Message</div></a>
					<ul>                
						<li ico="ico_list.jpg"><a href="/admin/page/user/guest_msg_list"><div>Guest Message List</div></a></li>						
					</ul>
				</li>
				<li ico="ico_list.jpg">
					<a href="javascript:void(0)"><div>Lottery Reports</div></a>
					<ul>                
						<li ico="ico_list.jpg"><a href="/admin/page/lottery/waiting_list_view"><div>Waiting List Summary</div></a></li>						
					</ul>
				</li>
            </ul>
        </li>
		<li>
            <a href="javascript:void(0)"><div>Configuration</div></a>
            <ul>                
                <li ico="ico_site_setting.jpg"><a href="/admin/page/setting/list_view"><div>System Configuration</div></a></li>
				<li ico="ico_site_setting.jpg"><a href="/admin/page/sms_setting/list_view"><div>SMS Lottery Auto Send</div></a></li>
            </ul>
        </li>		
    </ul>    
    
    <br style="clear: both;" />
</div>
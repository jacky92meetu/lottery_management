<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	$CI =& get_instance();		
	$temp = $CI->cpage->get_page_title();
	$temp2 = $CI->cpage->get_sub_menu();
	if(strlen($temp)==0 && empty($temp2)){
		return;
	}
	$CI->lang->load('menu');
?>

<style>	
	.menu_container a{
		border:0;
		padding:0;
		margin:0;
		text-decoration:none;
	}
	.menu_container div{
		width:50px;
		height:50px;
		border:1px #DDDDDD solid;
		text-align:center;
	}
	.menu_container div.hover{
		width:50px;
		height:50px;
		border:1px #00dd01 solid;
		text-align:center;
	}
</style>

<script>
	jQuery(function(){		
		jQuery('.menu_container a').hover(
		function(){
			var $obj = jQuery(this);			
			$obj.find('div').addClass('hover');			
		},
		function(){
			var $obj = jQuery(this);
			$obj.find('div').removeClass('hover');			
		});
	});
</script>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td align="center" valign="top" style="height:60px;border-bottom:1px #BBBBBB solid;padding:0 5px;">
			<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td align="left" valign="center" style="font:bold italic 24px verdana;">
						<?php echo $temp; ?>
					</td>
					<td align="right" valign="top" class="menu_container">
						<table cellpadding="0" cellspacing="0" border="0">
							<tr>
								<?php
									if(sizeof($temp2)>0){
										foreach($temp2 as $key => $url){
											$img = $CI->lang->image('small_'.$key);										
											echo'<td align="center" valign="top" style="width:50px;padding:0 5px;overflow:hidden;"><a class="show_tool_tips" desc="'.$CI->lang->line('desc_'.$key).'" href="'.$url.'"><div style="background: url('.$img.') no-repeat 50% 50%"></div></a></td>';
										}
									}
								?>								
							</tr>
						</table>						
					</td>
				</tr>
			</table>	
		</td>
	</tr>
</table>
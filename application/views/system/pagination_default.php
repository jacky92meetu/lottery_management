<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<style>	
    .pagination img{
		font:normal 11px arial;
		text-align: center;
		border:none;		
    }    
    .pagination .active{
        border:1px solid #E2E2E2;background:#bdd0d0;padding:5px;text-align:center;margin:1px;
    }      
	.pagination a, .pagination a:link, .pagination a:visited, .pagination span{
		color:#333;
		border:none;
		text-decoration: none;		
	}	
    .pagination ul.pages{
        margin: 0;
		padding: 0;
		list-style-type: none;
    }	
	.pagination ul.pages li{
		position: relative;
		display: inline;		
		float: left;
    }	
	.pagination ul.pages li a, .pagination ul.pages li span{
		display: block;
		width: 20px;		
		font:normal 12px arial;
		text-align: center;		
		margin:1px;
		padding:1px;
	}	
    .pagination ul.pages li a:hover{
		margin:0;
        border:1px #818080 solid;
    }    
    .pagination ul.pages li span.selected{	
		margin:0;
        border:1px #0000FF solid;
    }
</style>

<div class="pagination" style="margin:0 auto;">
    <table border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td width="" align="left" valign="top">
				<?php if($list['prev']['active']){ ?>
				<a href="<?php echo $list['prev']['link']; ?>">
					<table width="" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="" align="left" valign="top"><img src="/application/globals/images/system/pagination/page_icon_14.png" width="22" height="22" /></td>
						<td width="80px" align="center" valign="middle" style="background:url(/application/globals/images/system/pagination/page_body_01.png) no-repeat top right; font-size:12px; font:Arial, Helvetica, sans-serif; padding:0px 5px;"><?php echo $this->lang->line('CC PREVIOUS PAGE'); ?></td>
					</tr>
					</table>
				</a>
				<?php }else{ ?>
					<table width="" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width="" align="left" valign="top"><img src="/application/globals/images/system/pagination/page_icon_13.png" width="22" height="22" /></td>
							<td width="80px" align="center" valign="middle" style="background:url(/application/globals/images/system/pagination/page_body_01.png) no-repeat top right; font-size:12px; font:Arial, Helvetica, sans-serif; padding:0px 5px;"><?php echo $this->lang->line('CC PREVIOUS PAGE'); ?></td>
						</tr>
					</table>
				<?php } ?>
			</td>                        
			<td width="" align="left" valign="top">
				<table width="" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="10" height="22" align="left" valign="top" style="background:url(/application/globals/images/system/pagination/page_body_02.png) no-repeat top left; font-size:14px; font:Arial, Helvetica, sans-serif;">&nbsp;</td>
						<td align="center" valign="middle" style="background:url(/application/globals/images/system/pagination/page_body_03.png) repeat-x top left;">
							<ul class="pages">								
								<?php
									if(ISSET($list['begin']['active']) && $list['begin']['active']){
										echo'<li><a href="'.$list['begin']['link'].'">'.$list['begin']['text'].'</a></li>';
										echo'<li><a href="'.$list['begin']['link2'].'">...</a></li>';
									}
									foreach($list['pages'] as $page){
										if($page['active']){
											echo'<li><a href="'.$page['link'].'">'.$page['text'].'</a></li>';
										}else{
											echo'<li><span class="selected">'.$page['text'].'</span></li>';
										}
									}
									if(ISSET($list['end']['active']) && $list['end']['active']){
										echo'<li><a href="'.$list['end']['link2'].'">...</a></li>';
										echo'<li><a href="'.$list['end']['link'].'">'.$list['end']['text'].'</a></li>';
									}
								?>
							</ul>
						</td>
						<td width="10" align="left" valign="top" style="background:url(/application/globals/images/system/pagination/page_body_01.png) no-repeat top right;">&nbsp;</td>
					</tr>
				</table>
			</td>
			<td width="" align="right" valign="top">
				<?php if($list['next']['active']){ ?>
				<a href="<?php echo $list['next']['link']; ?>">
					<table width="" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="80px" align="center" valign="middle" style="background:url(/application/globals/images/system/pagination/page_body_02.png) no-repeat top left; font-size:12px; font:Arial, Helvetica, sans-serif; padding:0px 5px;"><?php echo $this->lang->line('CC NEXT PAGE'); ?></td>
							<td width="" align="left" valign="top"><img src="/application/globals/images/system/pagination/page_icon_04.png" width="22" height="22" /></td>
					</tr>
					</table>
				</a>
				<?php }else{ ?>
					<table width="" border="0" cellspacing="0" cellpadding="0">
						<tr>                                        
							<td width="80px" align="center" valign="middle" style="background:url(/application/globals/images/system/pagination/page_body_02.png) no-repeat top left; font-size:12px; font:Arial, Helvetica, sans-serif; padding:0px 5px;"><?php echo $this->lang->line('CC NEXT PAGE'); ?></td>
							<td width="" align="right" valign="top"><img src="/application/globals/images/system/pagination/page_icon_03.png" width="22" height="22" /></td>
						</tr>
					</table>
				<?php } ?>
			</td>
		</tr>
	</table>
</div>

<div style="clear: both"></div>
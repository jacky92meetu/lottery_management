<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<style>
	a{
		padding:0;
		margin:0;		
		border:none;		
	}
	img{
		border:none;
	}	
	.table_list{
		border:1px #CCCCCC solid;
		border-collapse: collapse;
	}
	table.table_list tr{		
		background:#deecff;		
	}	
	table.table_list tr:nth-child(even){
		background: #ffffff;		
	}
	table.table_list th{
		border-right: 1px #CCCCCC solid;
		border-bottom: 2px #CCCCCC solid;
		background:#DADADA;
		color: #000000;
		padding: 5px 0;
		font: bold 16px arial;
	}	
	table.table_list td{
		border-right: 1px #CCCCCC solid;
		padding: 5px;		
		font: normal 14px arial;
		text-align:center;
	}	
	table.table_list td:nth-child(last){
		border:none;		
	}	
	table th.orderby_selected{
		color:#f4a226;		
	}	
	.arial_14px_bold_0{
		font: bold 14px arial;
		color: #000000;
	}
	.arial_14px_bold_1{
		font: bold 14px arial;
		color: #0000FF;
	}	
		
</style>

<script>
	var $listTemplate = new listTemplate({
		'publish_0':'<?php echo $this->CI->lang->image('ico_publish_0'); ?>',
		'publish_1':'<?php echo $this->CI->lang->image('ico_publish_1'); ?>',
		'block_0':'<?php echo $this->CI->lang->image('ico_block_0'); ?>',
		'block_1':'<?php echo $this->CI->lang->image('ico_block_1'); ?>'
	});
</script>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>
			<div>
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td align="center" valign="top" style="background-color: #999;" class="filter_link">			
						<a href="javascript:void(0);"><font class="arial_14px_bold_0">FILTER</font></a>
					</td>
				</tr>
				<tr>
					<td style="padding:0 0 5px 0;">
						<div class="filter_container" style="padding:0 0 5px 0;">
							<table width="100%" cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td align="right" valign="top" style="border:1px #CCCCCC solid;padding:5px;">
										<form id="form_search" method="GET" onSubmit="return $listTemplate.form_search_validate()">
											<input type="hidden" id="ourl" value="<?php echo $this->CI->cpagination->get_link(); ?>">
											<table width="100%" cellpadding="0" cellspacing="0" border="0">							
											<tr>
												<td>
													<div class="filter_list">
														<?php
															if(isset($data['header']) && sizeof($data['header'])>0){																
																foreach($data['header'] as $header){									
																	echo'<div class="group" style="float:left;padding:0 5px;">';
																	$tf = $this->CI->cpagination->get_var('ff');
																	$tt = $this->CI->cpagination->get_var('fv');									
																	preg_match_all("#\{(.*)\},#Ui", $tf, $matches);
																	if(isset($matches[1])){
																		$tf = $matches[1];
																	}else{
																		$tf = null;
																	}
																	preg_match_all("#\{(.*)\},#Ui", $tt, $matches);
																	if(isset($matches[1])){
																		$tt = $matches[1];
																	}else{
																		$tt = null;
																	}
																	$text = "";
																	for($i=0; $i<sizeof($tf); $i++){
																		if($tf[$i]==$header['name']){											
																			$text = $tt[$i];
																			break;
																		}
																	}
																	echo $header['desc'].': ';
																	echo'<input type="hidden" class="search_filter" value="'. $header['name'] .'">';
																	echo'<input type="text" class="search_query" value="'. $text .'">';
																	echo'</div>';
																}

															}
														?>			
													</div>
												</td>
											</tr>
											<tr>
												<td align="right" valign="bottom">
													<?php
														if(isset($data['header']) && sizeof($data['header'])>0){								
															echo'<input type="submit" id="btnSearch" value="Search">';
														}else{
															echo'<input type="submit" id="btnSearch" value="Show All">';
														}
													?>			
												</td>
											</tr>
											</table>
										</form>
									</td>
								</tr>
							</table>
						</div>				
					</td>
				</tr>
				</table>
			</div>
		</td>
	</tr>
	
<?php 
$limitstart = $this->CI->cpagination->get_count_start();
$pagelimit = $this->CI->cpagination->get_page_limit();
if(ISSET($data['records']) && sizeof($data['records'])>0){
	echo'
		<tr>
		<td>
			<form name="form1" method="POST">
			<input type="hidden" name="section" value="delete">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr><td align="center" valign="top">					
		';
		
	echo '<div style="float:right;padding:5px">			
			<font class="arial_14px_bold_0">page limit:</font>&nbsp;&nbsp;
			<a href="'.$this->CI->cpagination->get_link(array("cur_page"=>1,"page_limit"=>20)).'" class="'.(($pagelimit==20)?"arial_14px_bold_1":"").'">20</a>
			<a href="'.$this->CI->cpagination->get_link(array("cur_page"=>1,"page_limit"=>50)).'" class="'.(($pagelimit==50)?"arial_14px_bold_1":"").'">50</a>
			<a href="'.$this->CI->cpagination->get_link(array("cur_page"=>1,"page_limit"=>90)).'" class="'.(($pagelimit==90)?"arial_14px_bold_1":"").'">90</a>
			<a href="'.$this->CI->cpagination->get_link(array("cur_page"=>1,"page_limit"=>150)).'" class="'.(($pagelimit==150)?"arial_14px_bold_1":"").'">150</a>
			</div>';   
    echo'</td></tr>';
	
	echo'<tr><td align="center" valign="top">';	
	echo '<table width="100%" class="table_list" cellpadding="0" cellspacing="0" border="0">';
	echo '<tr>';	
	echo '<th>No</th>';
	foreach($data['header'] as $header){
		$temp = $header['desc'];
		if(ISSET($header['order_link']) && strlen($header['order_link'])>0){
			$temp .= '&nbsp;<a href="'.$header['order_link'].'"><img src="'.$this->lang->image('ico_'.$header['os']).'" style="width:16px;height:16px;"></a>';
		}
		if($header['status']){
			echo '<th class="orderby_selected">'.$temp.'</th>';
		}else{
			echo '<th>'.$temp.'</th>';
		}		
	}	
		
	echo '<th>Action</th>';
	echo '</tr>';

	$count = $limitstart;	
	$ajax_listed = array();
	foreach($data['records'] as $rec){		
		echo '<tr id="item_'.$rec['data']['id'].'">';		
		echo '<td>'.$count.'</td>';
		
		foreach($data['header'] as $header){			
			if(ISSET($rec['ajax_action'][$header['name']])){
				if(!(ISSET($rec['data']['core']) && $rec['data']['core']==1)){
					$ajax_listed[] = $header['name'];
					$a = $rec['ajax_action'][$header['name']];
					if($a['type']=="func"){						
						$temp = '<a class="func" href="'.$a['link'].'"><span id="'.$header['name'].'_'.$rec['data']['id'].'">'.$rec['data'][$header['name']].'</span></a>';
					}else if($a['type']=="dialog"){
						$temp = '<a class="dialog" href="'.$a['link'].'"><span id="'.$header['name'].'_'.$rec['data']['id'].'">'.$rec['data'][$header['name']].'</span></a>';
					}else{
						$temp = '<a class="func show_tool_tips" desc="'.$this->lang->line('desc_'.$header['name']).'" href="'.$a['link'].'"><span id="'.$header['name'].'_'.$rec['data']['id'].'"><img src="'.$this->lang->image('ico_'.$header['name'].'_'.$rec['data'][$header['name']]).'" style="width:16px;height:16px;"></span></a>';
					}
				}else{
					/*$temp = '<img src="'.$this->lang->image('ico_'.$header['name'].'_'.$rec['data'][$header['name']]).'" style="width:16px;height:16px;">';*/
					$temp = $rec['data'][$header['name']];
				}				
			}else if($header['im']){
				$temp = '<img src="'.$rec['data'][$header['name']].'" style="width:100px;">';
			}else{
				$temp = $rec['data'][$header['name']];
			}
			echo '<td>'.$temp.'</td>';
		}
		
		$temp = "";
		if(!(ISSET($rec['data']['core']) && $rec['data']['core']==1)){
			if(ISSET($rec['ajax_action'])){
				foreach($rec['ajax_action'] as $key => $value){
					if(array_search($key, $ajax_listed)===false){
						if($value['type']=="dialog"){
							$temp .= '&nbsp;<a class="dialog show_tool_tips" desc="'.$this->lang->line('desc_'.$value['name']).'" href="'.$value['link'].'"><img src="'.$this->lang->image('ico_'.$value['name']).'" style="padding:2px;width:16px;height:16px;"></a>';
						}else if($value['type']=="func"){
							$temp .= '&nbsp;<a class="func show_tool_tips" desc="'.$this->lang->line('desc_'.$value['name']).'" href="'.$value['link'].'"><img src="'.$this->lang->image('ico_'.$value['name']).'" style="padding:2px;width:16px;height:16px;"></a>';
						}else if($value['type']=="link"){
							$temp .= '&nbsp;<a class="show_tool_tips" desc="'.$this->lang->line('desc_'.$value['name']).'" href="'.$value['link'].'"><img src="'.$this->lang->image('ico_'.$value['name']).'" style="padding:2px;width:16px;height:16px;"></a>';
						}
					}
				}
			}
			if(ISSET($rec['action'])){
				foreach($rec['action'] as $a){
					if(!ISSET($rec['data'][$a['name']])){
						$temp .= '&nbsp;<a class="show_tool_tips" desc="'.$this->lang->line('desc_'.$a['name']).'" href="'.$a['link'].'"><img src="'.$this->lang->image('ico_'.$a['name']).'" style="padding:2px;width:16px;height:16px;"></a>';
					}				
				}				
			}				
		}
		if(strlen($temp)>0){
			echo '<td>'.$temp.'</td>';			
		}else{
			echo '<td>&nbsp;</td>';			
		}
		
		echo '</tr>';		
		$count += 1;
	}	
	echo '</table>';    
	
	echo '<div style="text-align:left;">'.$this->CI->cpagination->get_total().' record(s) found.</div>';	
    
	echo '<div style="padding:5px;">'.$this->CI->cpagination->get_view().'</div>';		
	
	echo '
		</table>
		</form>
		</td>
	</tr>
	';
}else{
	echo'
	<tr>
		<td align="left">No Record!</td>
	</tr>
	';
}
?>
			
</table>
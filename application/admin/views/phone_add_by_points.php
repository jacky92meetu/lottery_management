<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	$CI = & get_instance();
	$CI->load->library('cuser_points');
	$CI->cpage->set_javascript("myScript_DValidation.js");
?>

<script>
	jQuery(function(){
		jQuery('select[name="form_group"]').change(function(){
			calculate();
		});
		jQuery('input[name="qty"]').keyup(function(){
			calculate();
		});
		calculate();
	});
	
	function calculate(){
		var $obj = jQuery('select[name="form_group"]');
		var $price = 0;
		var $qty = 0;
		var $total = 0;
		$price = $obj.find('option:selected').attr('cprice');
		$qty = jQuery('input[name="qty"]').val();		
		if($qty==""){
			$qty = 0;
		}
		$total = ($qty*1)*($price*1);
		jQuery('#total_credit').html($total.toFixed(2));
	}
	
	function validation(){
		if(confirm("Confirm to add this subscription?")){
			jQuery('form[name="form1"]').submit();			
		}
	}
</script>

<script>
jQuery(function(){
	jQuery('#btnBack').click(function(){
		history.back();
	});
});
</script>

<form name="form1" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
	<input type="hidden" name="section" value="add">
<table cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td align="left" valign="top" style="padding:5px;">
			<table class="std_form1" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td align="right" class="label">Subscribe Package</td>					
					<td align="left" class="data">
					<?php 
						$contents = '';
						if(isset($data['group'][0])){			
							foreach($data['group'] as $key => $value){
								$contents .= '<OPTION value="'.$value['cname'].'" cprice="'.$CI->cuser_points->get_product_points($value['cname']).'" ';
								if($data['form_group']==$value['cname']){
									$contents .= 'SELECTED';
								}					
								$contents .= '>['.$value['cdesc'].'] - '.$CI->cuser_points->get_product_display_points($value['cname']).' '.$CI->lang->line('default_points_name').'</OPTION>';
							}
						}
						if(strlen($contents)>0){
							$contents = '<SELECT name="form_group">'.$contents;
							$contents = $contents.'</SELECT>';
						}
						echo $contents;
					?>
					</td>
				</tr>				
				<tr>
					<td align="right" class="label">Quantity</td>					
					<td align="left" class="data">
						<input type="text" name="qty" maxlength="100" size="50" class="DValidation DValidation_focus" vtype="regexp:/^[1-9]+[0-9]*$/" vmsg="Please enter valid number. min = 1" value="<?php echo $data['qty']; ?>">
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Total <?php echo $CI->lang->line('default_points_name'); ?> Needed</td>					
					<td align="left" class="data">
						<div id="total_credit"></div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<hr style="width:100%;height:1px;border:0;background:#EEEEEE;">
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Phone No.</td>					
					<td align="left" class="data">
						<input type="text" name="phone" maxlength="11" size="50" class="DValidation" vtype="regexp:/^01[0-9]{8,9}$/" vmsg="Please enter valid mobile number. e.g.: 0129998888" value="<?php echo $data['phone']; ?>">
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Username</td>					
					<td align="left" class="data">						
						<input type="text" name="username" maxlength="100" size="50" class="DValidation" vtype="required" vmsg="Please enter username" value="<?php echo $data['username']; ?>">
					</td>
				</tr>				
				
				<tr>
					<td	colspan="2" class="button">
						<input type="button" id="btnSubmit" name="btnSubmit" class="DValidation_button" value="Add" func="validation()">
						<input type="button" id="btnBack" name="btnBack" value="Back">
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
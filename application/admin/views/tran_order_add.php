<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	$CI = & get_instance();
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
		var $qty2 = 0;
		var $total = 0;
		var $total2 = 0;
		$price = $obj.find('option:selected').attr('cprice');
		$qty2 = $obj.find('option:selected').attr('cqty');
		if($qty2=="" || $qty2==undefined){
			$qty2 = 0;
		}
		$qty = jQuery('input[name="qty"]').val();		
		if($qty=="" || $qty==undefined){
			$qty = 0;
		}
		$total2 = ($qty2*1)*($qty*1);
		$total = ($qty*1)*($price*1);
		jQuery('#total_credit').html($total2.toFixed(0));
		jQuery('#total_amount').html($total.toFixed(2));
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
	<input type="hidden" name="date" value="<?php echo $data['date']; ?>">
<table cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td align="left" valign="top" style="padding:5px;">
			<table class="std_form1" cellspacing="0" cellpadding="0" border="0">						
				<tr>
					<td align="right" class="label">Transaction Date</td>					
					<td align="left" class="data">
						<?php echo $data['date']; ?>
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Product/Service</td>					
					<td align="left" class="data">
					<?php 
						$contents = '';
						if(isset($data['group'][0])){			
							foreach($data['group'] as $key => $value){
								$contents .= '<OPTION value="'.$value['ccode'].'" cqty="'.$value['cuqty'].'" cprice="'.$value['cuprice'].'" ';
								if($data['form_group']==$value['ccode']){
									$contents .= 'SELECTED';
								}					
								$contents .= '>['.$value['cdesc'].']</OPTION>';
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
						<input type="text" name="qty" maxlength="100" size="50" class="DValidation" vtype="regexp:/^([1-9][0-9]*)|(([1-9][0-9]*)\.([1-9][0-9]*))$/" vmsg="Incorrect quantity" value="<?php echo $data['qty']; ?>">
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Total <?php echo $CI->lang->line('default_points_name'); ?></td>					
					<td align="left" class="data">
						<div id="total_credit"></div>
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Total Amount</td>					
					<td align="left" class="data">
						<div id="total_amount"></div>
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Username</td>					
					<td align="left" class="data">						
						<input type="text" name="username" maxlength="100" size="50" class="DValidation" vtype="required" vmsg="Please enter username" value="<?php echo $data['username']; ?>">
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Comments</td>					
					<td align="left" class="data">						
						<textarea name="comments" cols=50 rows=10><?php echo $data['comments']; ?></textarea>
					</td>
				</tr>
				<tr>
					<td	colspan="2" class="button">
						<input type="submit" id="btnSubmit" name="btnSubmit" class="DValidation_button" value="Add">
						<input type="button" id="btnBack" name="btnBack" value="Back">
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
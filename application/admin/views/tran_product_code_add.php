<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	$CI = & get_instance();
	$CI->cpage->set_javascript("myScript_DValidation.js");
?>

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
					<td align="right" class="label">Transaction Code</td>					
					<td align="left" class="data">
						<input type="text" name="code" maxlength="100" size="50" class="DValidation DValidation_focus" vtype="strlen:3" vmsg="Please enter transaction code" value="<?php echo $data['code']; ?>">
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Description</td>					
					<td align="left" class="data">
						<input type="text" name="desc" maxlength="100" size="50" class="DValidation" vtype="strlen:3" vmsg="Please enter transaction code description" value="<?php echo $data['desc']; ?>">
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Unit Quantity</td>					
					<td align="left" class="data">
						<input type="text" name="qty" maxlength="100" size="50" class="DValidation" vtype="regexp:/^([1-9][0-9]*)|(([1-9][0-9]*)\.([1-9][0-9]*))$/" vmsg="Incorrect quantity" value="<?php echo $data['qty']; ?>">
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Unit Price</td>					
					<td align="left" class="data">
						<input type="text" name="price" maxlength="100" size="50" class="DValidation" vtype="regexp:/^([1-9][0-9]*)|(([1-9][0-9]*)\.([1-9][0-9]*))?$/" vmsg="Incorrect price" value="<?php echo $data['qty']; ?>">
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
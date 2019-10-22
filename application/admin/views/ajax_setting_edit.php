<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<script>
	function validation(){
		var $id = "<?php echo $data['id']; ?>";
		var $value = jQuery('.DValidation[name="value"]').val();		
		var $url = 'ajax_confirm_edit/?id='+encodeURI($id)+'&val='+encodeURI($value);		
		JFunc.Jax.dialog($url,'$$owner');
	}
</script>

<form name="form1" method="POST">
	<input type="hidden" name="section" value="edit">
<table cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td align="left" valign="top" style="padding:5px;">
			<table class="std_form1" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td align="right" class="label">Name</td>					
					<td align="left" class="data"><?php echo $data['name']; ?></td>
				</tr>
				<tr>
					<td align="right" class="label">Value</td>					
					<td align="left" class="data">
<!--						<input type="text" name="value" maxlength="100" size="50" class="DValidation DValidation_focus" vtype="required" vmsg="Please key in the value" value="<?php echo $data['value']; ?>">-->
						<textarea name="value" cols="50" rows="5" class="DValidation DValidation_focus" vtype="required" vmsg="Please key in the value"><?php echo $data['value']; ?></textarea>
					</td>
				</tr>
				<tr>
					<td	colspan="2" class="button">
						<input type="submit" id="btnSubmit" name="btnSubmit" class="DValidation_button" func="validation()" value="Save">						
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
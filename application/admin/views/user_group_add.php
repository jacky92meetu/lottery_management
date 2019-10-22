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
					<td class="label" align="right">Group Name</td>					
					<td class="data" align="left">
						<input type="text" name="desc" maxlength="100" size="50" class="DValidation DValidation_focus" vtype="strlen:3" vmsg="Please enter the group name and character length must greater than 3 characters" value="<?php echo $group['desc']; ?>">
					</td>
				</tr>				
				<tr>
					<td class="label" align="right">Publish?</td>					
					<td class="data" align="left">
						<input type="checkbox" name="publish" CHECKED>
					</td>
				</tr>
				<tr>					
					<td colspan="2" class="button">
						<input type="submit" id="btnSubmit" name="btnSubmit" class="DValidation_button" value="Add">
						<input type="button" id="btnBack" name="btnBack" value="Back">
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
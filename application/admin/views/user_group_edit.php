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
	<input type="hidden" name="section" value="edit">
<table cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td align="left" valign="top" style="padding:5px;">
			<table class="std_form1" cellspacing="0" cellpadding="0" border="0">							
				<tr>
					<td align="right" class="label">Group Name</td>					
					<td align="left" class="data">
						<input type="text" name="desc" maxlength="100" size="50" class="DValidation DValidation_focus" vtype="strlen:3" vmsg="Please enter the group name and character length must greater than 3 characters" value="<?php echo $group['desc']; ?>">
					</td>
				</tr>				
				<tr>
					<td align="right" class="label">Publish?</td>					
					<td align="left" class="data">
						<input type="checkbox" name="publish" <?php echo (($group['publish']==1)?"CHECKED":""); ?>>
					</td>
				</tr>
				<tr>					
					<td colspan="2" class="button">
						<input type="submit" id="btnSubmit" name="btnSubmit" class="DValidation_button" value="Save">
						<input type="button" id="btnBack" name="btnBack" value="Back">
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	$CI = & get_instance();
	$CI->cpage->set_javascript("myScript_DValidation.js");
?>

<script>
jQuery(function(){
	jQuery('.btnBack').click(function(){
		history.back();
	});
});
</script>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td align="center" valign="top" style="width:50%;padding:5px;border-right:5px #666666 solid;">
			<form name="form1" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
			<input type="hidden" name="section" value="details">
			<table class="std_form1" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td align="right" class="label">User Group</td>					
					<td align="left" class="data"><?php echo $group; ?></td>
				</tr>				
				<tr>
					<td align="right" class="label">Display Name</td>					
					<td align="left" class="data">
						<input type="text" name="name" maxlength="100" size="50" class="DValidation DValidation_focus" vtype="strlen:3" vmsg="Please enter your display name and character length must greater than 3 characters" value="<?php echo $user->display_name; ?>">
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Username</td>					
					<td align="left" class="data">
						<?php echo $user->username; ?>
					</td>
				</tr>			
				<tr>
					<td align="right" class="label">Email Address</td>					
					<td align="left" class="data">
						<input type="text" name="email" maxlength="255" size="50" class="DValidation" vtype="email" vmsg="Please enter your valid email address" value="<?php echo $user->email; ?>">
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Mobile No</td>					
					<td align="left" class="data">
						<input type="text" name="cmobile" maxlength="100" size="50" value="<?php echo $user->cmobile; ?>">
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Referrer Username</td>					
					<td align="left" class="data">
						<input type="text" name="referer" maxlength="100" size="50" value="<?php echo $referrer[0]['username']; ?>">
					</td>
				</tr>
				<tr>
					<td colspan="2" class="button">
						<input type="submit" id="btnSubmit" name="btnSubmit" class="DValidation_button" value="Save">						
						<input type="button" value="Back" class="btnBack">
					</td>
				</tr>
			</table>
			</form>
		</td>	
		<td align="center" valign="top" style="padding:5px;">			
			<form name="form2" action="<?php echo $this->CI->uri->ruri_string(); ?>" method="POST">
				<input type="hidden" name="section" value="password">
			<table class="std_form1" cellspacing="0" cellpadding="0" border="0">				
				<tr>
					<td align="right" class="label">Password</td>					
					<td align="left" class="data">
						<input type="password" id="password" name="password" maxlength="100" size="50" class="DValidation" vtype="strlen:6" vmsg="Please enter your password and character length must greater than 6 characters">
					</td>
				</tr>				
				<tr>
					<td align="right" class="label">Confirm Password</td>					
					<td align="left" class="data">
						<input type="password" name="password2" maxlength="100" size="50" class="DValidation" vtype="match:password" vmsg="Password not match">
					</td>
				</tr>								
				<tr>
					<td colspan="2" class="button">
						<input type="submit" id="btnSubmit" name="btnSubmit" class="DValidation_button" value="Change Password">						
						<input type="button" value="Back" class="btnBack">
					</td>
				</tr>
			</table>
			</form>			
		</td>
	</tr>
</table>
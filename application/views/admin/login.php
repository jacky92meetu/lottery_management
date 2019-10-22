<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
$CI = & get_instance();
$CI->cpage->set_javascript("myScript_DValidation.js");
?>

<div style="width:100%;padding:100px 0 0 0;">	
	<form name="form1" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
	<input type="hidden" name="section" value="login">
	<table width="100%" height="100%">
		<tr>
			<td align="center" valign="center">
				<div style="width:550px;border:5px #3dc1e8 solid;">
					<div style="background:#3dc1e8;color:#ffffff;font:bold 36px arial;padding:5px;border-bottom:5px solid #DADADA;">Administrator Login</div>
					<div style="padding:20px;">						
						<table width="100%"cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="center" valign="top">									
									<div>
										<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
										<tr>
											<td align="right" valign="top" style="width:150px;padding:0 10px;">Username: </td>
											<td><input type="text" size="30" name="username" class="DValidation DValidation_focus" vtype="required" vmsg="Please enter your username"></td>
										</tr>
										<tr>
											<td align="right" valign="top" style="padding:0 10px;">Password: </td>
											<td><input type="password" size="30" name="password" class="DValidation" vtype="required" vmsg="Please enter your password"></td>
										</tr>							
										</table>						
									</div>									
								</td>
							</tr>
							<tr>
								<td align="center" valign="top" style="padding:10px;">
									<input type="submit" class="DValidation_button" name="btnSubmit" value="Login">
								</td>
							</tr>
						</table>						
					</div>					
				</div>
			</td>
		</tr>
	</table>
	</form>
</div>

<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<form name="form1" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
	<input type="hidden" name="section" value="send">
<table cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td align="left" valign="top" style="padding:5px;">
			<table class="std_form1" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td align="right" class="label">Result Type</td>					
					<td align="left" class="data"><?php echo $data['group']; ?></td>
				</tr>
				<tr>
					<td align="right" class="label">Result Date</td>					
					<td align="left" class="data">
						<input type="text" name="cdate" maxlength="20" size="50" class="DValidation DValidation_focus" vtype="date" vmsg="Please key in the date. e.g.: 2012-04-22" value="<?php echo $data['cdate']; ?>">
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Phone Number</td>					
					<td align="left" class="data">
						<input type="text" name="pmobile" maxlength="20" size="50" class="DValidation" vtype="regexp:/^\d+$/i" vmsg="Please key in the phone number. e.g.: 0123456789" value="<?php echo $data['pmobile']; ?>">
					</td>
				</tr>
				<tr>
					<td	colspan="2" class="button">
						<input type="submit" id="btnSubmit" name="btnSubmit" class="DValidation_button" value="Send">						
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
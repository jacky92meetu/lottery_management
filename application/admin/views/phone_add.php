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
					<td align="right" class="label">Subscribe Package</td>					
					<td align="left" class="data">
					<?php 
						$contents = '';
						if(isset($data['group'][0])){			
							foreach($data['group'] as $key => $value){
								$contents .= '<OPTION value="'.$value['cname'].'" ';
								if($name==$value['cname']){
									$contents .= 'SELECTED';
								}					
								$contents .= '>'.$value['cname'].' - '.$value['cdesc'].'</OPTION>';
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
					<td align="right" class="label">Phone No.</td>					
					<td align="left" class="data">
						<input type="text" name="phone" maxlength="11" size="50" class="DValidation DValidation_focus" vtype="regexp:/^01[0-9]{8,9}$/" vmsg="Please enter valid mobile number. e.g.: 0129998888" value="<?php echo $data['phone']; ?>">
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Quantity</td>					
					<td align="left" class="data">
						<input type="text" name="qty" maxlength="100" size="50" class="DValidation" vtype="regexp:/^[1-9]+[0-9]*$/" vmsg="Please enter valid number. min = 1" value="<?php echo $data['qty']; ?>">
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Username</td>					
					<td align="left" class="data">
						<input type="text" name="username" maxlength="100" size="50" value="<?php echo $data['username']; ?>">
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Notify Subscriber?</td>					
					<td align="left" class="data">
						<input type="checkbox" name="notified" CHECKED>
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
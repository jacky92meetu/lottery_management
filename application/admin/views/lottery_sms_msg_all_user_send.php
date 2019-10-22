<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<script>
	function check_msg_len($obj,$out){
		var $text = $obj.val().replace(/[^\x0a\x20-\x7e]/i, "");
		$obj.val($text);
		var $len = $text.length;
		$out.html($len);
	}
	function validation(){
		if(confirm("Confirm to send to all user?")){
			jQuery('form[name="form1"]').submit();			
		}
	}
	jQuery(function(){
		var $obj = jQuery('textarea[name="msg"]');
		var $out = jQuery('#flen');		
		$obj.keyup(function(){
			check_msg_len($obj,$out);
		});
		check_msg_len($obj,$out);
	});	
</script>

<form name="form1" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
	<input type="hidden" name="section" value="send">
<table cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td align="left" valign="top" style="padding:5px;">
			<table class="std_form1" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td align="right" class="label">Message</td>					
					<td align="left" class="data">
						<div>
							<textarea name="msg" class="DValidation" vtype="required" vmsg="Please enter the message" rows="10" cols="30"><?php echo $data['msg']; ?></textarea>
							<div style="text-align:left;"><span id="flen">0</span> Character(s).</div>
						</div>						
					</td>
				</tr>
				<tr>
					<td	colspan="2" class="button">
						<input type="button" id="btnSubmit" name="btnSubmit" class="DValidation_button" value="Send" func="validation()">
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
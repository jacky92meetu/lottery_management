<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	$CI = & get_instance();
	$CI->cpage->set_javascript("myScript_DValidation.js");
	//$types
?>

<script>
function validation(){
	if(confirm("Are you confirm to add this new result?")){
		jQuery('form[name="form1"]').submit();
	}	
}
</script>

<form name="form1" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
	<input type="hidden" name="section" value="add">
	<input type="hidden" name="cfrom" value="<?php echo $types[0]['cfrom']; ?>">	
	<input type="hidden" name="lottery_type" value="<?php echo $data['lottery_type']; ?>">	
<table cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td align="left" valign="top" style="padding:5px;">
			<table class="std_form1" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td align="right" class="label">Date</td>					
					<td align="left" class="data">
						<input type="text" name="date" maxlength="50" size="50" class="DValidation DValidation_focus" vtype="date" vmsg="Please enter valid date. e.g.: 2012-04-30" value="<?php echo $data['date']; ?>">
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Draw No</td>					
					<td align="left" class="data">
						<input type="text" name="draw" maxlength="10" size="50" class="DValidation" vtype="regexp:/^\d+\/\d+$/i" vmsg="Please enter valid draw. e.g.: 001/12" value="<?php echo $data['draw']; ?>">
					</td>
				</tr>
				
				<?php
					foreach($types as $type){
						echo '<tr>';
						echo '<td align="right" class="label">'.$type['ctitle'].'</td>';
						echo '<td align="left" class="data">';
						$err_msg = "";
						$regexp = $type['cdigit_regexp'];
						$field_size = $type['cdigit_field_size'];
						for($i=0; $i<$type['cnum_count']; $i++){
							$fname = $type['cfrom']."_".$type['ctitle']."_".($i+1);
							if($type['cprize_fixed']==0){
								echo ($i+1).': <span><input type="text" name="'.$fname.'" maxlength="'.$field_size.'" size="'.$field_size.'" class="DValidation" vtype="regexp:/^'.$regexp.'$/i" vmsg="'.$err_msg.'" value="'.$data[$fname].'"></span><br>';								
							}else{
								echo '<span><input type="text" name="'.$fname.'" maxlength="'.$field_size.'" size="'.$field_size.'" class="DValidation" vtype="regexp:/^'.$regexp.'$/i" vmsg="'.$err_msg.'" value="'.$data[$fname].'"></span>';
							}
						}						
						echo '</td>';
						echo '</tr>';
					}
				?>				
				
				<tr>
					<td	colspan="2" class="button">
						<input type="button" id="btnSubmit" name="btnSubmit" class="DValidation_button" func="validation()" value="Add">		
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
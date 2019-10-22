<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<script>
	function validation(){
		var $id = "<?php echo $data['id']; ?>";
		var $table = "<?php echo $data['ctable']; ?>";
		var $drawid = encodeURIComponent(jQuery('input:[name="drawid"]').val());
		var $cdate = encodeURIComponent(jQuery('input:[name="cdate"]').val());		
		var $cprize = encodeURIComponent(jQuery('input:[name="cprize"]').val());
		var $cno = encodeURIComponent(jQuery('input:[name="cno"]').val());
		var $url = '/admin/page/lottery/ajax_confirm_edit/?table='+$table+'&id='+$id+'&cfrom=<?php echo $data['cfrom']; ?>&ctitle=<?php echo $data['ctitle']; ?>'+'&drawid='+$drawid+'&date='+$cdate+'&cprize='+$cprize+'&cno='+$cno;
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
					<td align="right" class="label">From</td>					
					<td align="left" class="data">
						<?php echo $data['cfrom']; ?>
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Title</td>					
					<td align="left" class="data">
						<?php echo $data['ctitle']; ?>
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Draw No.</td>					
					<td align="left" class="data">
						<input type="text" name="drawid" maxlength="20" size="50" class="DValidation" vtype="regexp:/^\d+\/\d+$/i" value="<?php echo $data['drawid']; ?>">
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Date</td>					
					<td align="left" class="data">
						<input type="text" name="cdate" maxlength="20" size="50" class="DValidation" vtype="date" value="<?php echo $data['cdate']; ?>">
					</td>
				</tr>				
				<tr>
					<td align="right" class="label">Prize Status</td>					
					<td align="left" class="data">
						<input type="text" name="cprize" maxlength="20" size="50" class="DValidation" vtype="regexp:/^[1-5]{1}$/i" value="<?php echo $data['cprize']; ?>">
					</td>
				</tr>
				<tr>
					<td align="right" class="label">Value</td>					
					<td align="left" class="data">
						<input type="text" name="cno" maxlength="20" size="50" class="DValidation DValidation_focus" vtype="regexp:/^<?php echo $regexp; ?>$/i" value="<?php echo $data['cno']; ?>">
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
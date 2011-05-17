<?php
/**
* Bowe codes settings!
*
*/

function bowe_codes_options(){
	$defaultcss = file_get_contents(BOWE_CODES_PLUGIN_URL.'/css/default.css');
	if($_POST['bc_default_css']){
		update_option( 'bc_default_css', $_POST['bc_default_css'] );
	}
	
	$bc_option = get_option('bc_default_css');
	if($bc_option =="") $bc_option = "no";
	?>
	<div class="wrap">
		<h2><?php _e('Bowe codes options','bowe-codes');?></h2>
		<div style="width:400px">
		<form action="" method="post">
			<table class="form-bowe-code">
				<tr>
					<td><label for="bc_default_css"><?php _e('Disable default css','bowe-codes');?></label></td>
					<td><input type="radio" name="bc_default_css" value="yes" <?php if($bc_option=="yes") echo 'checked';?>><?php _e('Yes','bowe-codes');?>&nbsp;
					<input type="radio" name="bc_default_css" value="no" <?php if($bc_option=="no") echo 'checked';?>><?php _e('No','bowe-codes');?></td>
				</tr>
				<tr>
					<td class="action-btn" colspan="2"><input type="submit" value="<?php _e('Save','bowe-codes');?>" class="button-primary"/></td>
				</tr>
			</table>
		</form>
		</div>
		<div class="css-file-view">
			<h3><?php _e('Content of default.css (for info)','bowe-codes');?></h3>
			<textarea id="css-file-src"><?php echo $defaultcss;?></textarea>
		</div>
	</div>
	<?php
}

?>
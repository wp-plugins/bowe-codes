<?php
/**
 * Bowe Codes - xprofile template
 *
 * You can copy/paste in your theme's folder to edit and overide the template
 *
 * @since  2.5
 * @package Bowe Codes
 */
?>
<?php if( bp_has_profile( bowe_codes_member_loop_xprofile_args() ) ): ?>

	<?php while ( bp_profile_groups() ) : bp_the_profile_group(); ?>
				
		<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>
					
			<p>
				
				<?php if( bowe_codes_member_loop_show_xprofile_label() ):?>
					<span class="xprofile_thead"><?php bp_the_profile_field_name();?></span>
				<?php endif;?>

				<span class="xprofile_content"><?php bp_the_profile_field_value();?></span>
			</p>

		<?php endwhile;?>
					
	<?php endwhile;?>

<?php endif;?>
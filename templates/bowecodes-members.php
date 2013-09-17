<?php
/**
 * Bowe Codes - Member(s) template
 *
 * You can copy/paste in your theme's folder to edit and overide the template
 *
 * @since  2.5
 * @package Bowe Codes
 */
?>
<div <?php bowe_codes_members_loop_class();?>>

	<ul <?php bowe_codes_members_loop_class( 'ul' );?>>	

	<?php while ( bp_members() ) : bp_the_member(); ?>

		<li <?php bowe_codes_member_class();?>>

		<?php if( bowe_codes_member_loop_show_avatar() ):?>

			<div class="bc_avatar">
				<a href="<?php bp_member_permalink(); ?>"><?php bowe_codes_member_loop_avatar(); ?></a>
			</div>
			
		<?php endif;?>

			<div class="user-infos">

				<?php do_action( 'bowe_codes_member_loop_before_name', bowe_codes_member_loop_backpat( 'bowe_codes_member_loop_before_name' ) );?>

				<h4>
					<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
				</h4>

				<?php do_action( 'bowe_codes_member_loop_after_name', bowe_codes_member_loop_backpat( 'bowe_codes_member_loop_after_name' ) );?>

				<?php if( bowe_codes_member_loop_show_xprofile() ) bowe_codes_member_loop_load_xprofile();?>

				<?php do_action( 'bowe_codes_member_loop_after_fields', bowe_codes_member_loop_backpat( 'bowe_codes_member_loop_after_fields' ) );?>

			</div>

		</li>

	<?php endwhile; ?>

	<?php do_action( 'bowe_codes_member_loop_after_content', bowe_codes_member_loop_backpat( 'bowe_codes_member_loop_after_content' ) );?>

	</ul>
	
</div>
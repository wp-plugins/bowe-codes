<?php
/**
 * Bowe Codes - Group members template
 *
 * You can copy/paste in your theme's folder to edit and overide the template
 *
 * @since  2.5
 * @package Bowe Codes
 */
?>
<div <?php bowe_codes_members_loop_class();?>>

	<?php if( bowe_codes_get_group_members_loop_pre_content() ):?>
	
		<h3><?php bowe_codes_group_members_loop_pre_content();?></h3>

	<?php endif;?>

	<ul <?php bowe_codes_members_loop_class( 'ul' );?>>	

	<?php while ( bp_group_members() ) : bp_group_the_member(); ?>

		<li <?php bowe_codes_member_class();?>>

		<?php if( bowe_codes_member_loop_show_avatar() ):?>

			<div class="bc_avatar">
				<a href="<?php bp_group_member_domain(); ?>"><?php bowe_codes_member_loop_avatar(); ?></a>
			</div>
			
		<?php endif;?>

			<div class="user-infos">

				<?php do_action( 'bowe_codes_group_member_loop_before_name', bowe_codes_member_loop_backpat( 'bowe_codes_group_member_loop_before_name' ) );?>

				<h4>
					<a href="<?php bp_group_member_domain(); ?>"><?php bp_group_member_name(); ?></a>
				</h4>

				<?php do_action( 'bowe_codes_group_member_loop_after_name', bowe_codes_member_loop_backpat( 'bowe_codes_group_member_loop_after_name' ) );?>

			</div>

		</li>

	<?php endwhile; ?>

	<?php do_action( 'bowe_codes_group_member_loop_after_content', bowe_codes_member_loop_backpat( 'bowe_codes_group_member_loop_after_content' ) );?>

	</ul>
	
</div>
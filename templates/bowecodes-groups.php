<?php
/**
 * Bowe Codes - Group(s) template
 *
 * You can copy/paste in your theme's folder to edit and overide the template
 *
 * @since  2.5
 * @package Bowe Codes
 */
?>
<div <?php bowe_codes_groups_loop_class();?>>

	<?php if( bowe_codes_get_groups_loop_pre_content() ):?>
	
		<h3><?php bowe_codes_groups_loop_pre_content();?></h3>

	<?php endif;?>

	<ul <?php bowe_codes_groups_loop_class( 'ul' );?>>	

	<?php while ( bp_groups() ) : bp_the_group(); ?>

		<li <?php bowe_codes_group_class();?>>

		<?php if( bowe_codes_group_loop_show_avatar() ):?>

			<div class="bc_avatar">
				<a href="<?php bp_group_permalink(); ?>"><?php bowe_codes_group_loop_avatar(); ?></a>
			</div>

		<?php endif;?>

			<div class="group-infos">

				<?php do_action( 'bowe_codes_group_loop_before_name', bowe_codes_group_loop_backpat( 'bowe_codes_group_loop_before_name' ) );?>

				<h4>
					<a href="<?php bp_group_permalink(); ?>"><?php bp_group_name(); ?></a>
				</h4>

				<?php do_action( 'bowe_codes_group_loop_after_name', bowe_codes_group_loop_backpat( 'bowe_codes_group_loop_after_name' ) );?>

				<?php if( bowe_codes_group_loop_show_description() ):?>

					<p>
						<span class="group-desc"><?php bp_group_description();?></span>
					</p>

				<?php endif;?>

				<?php do_action( 'bowe_codes_group_loop_after_description', bowe_codes_group_loop_backpat( 'bowe_codes_group_loop_after_description' ) );?>

			</div>

		</li>

	<?php endwhile; ?>

	<?php do_action( 'bowe_codes_group_loop_after_content', bowe_codes_group_loop_backpat( 'bowe_codes_group_loop_after_content' ) );?>

	</ul>

</div>
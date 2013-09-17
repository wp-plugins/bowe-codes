<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

add_action( 'bp_init',             'bowe_codes_init',                10 );
add_action( 'bowe_codes_init',     'bowe_codes_register',             0 );
add_action( 'bowe_codes_register', 'bowe_codes_register_shortcodes', 10 );
add_action( 'bp_enqueue_scripts',  'bowe_codes_enqueue_scripts',     10 );
add_action( 'bp_admin_init',       'bowe_codes_admin_init',          10 );
add_action( 'widgets_init',        'bowe_codes_widgets_init',       100 );

function bowe_codes_init() {
	do_action( 'bowe_codes_init' );
}

function bowe_codes_register() {
	do_action( 'bowe_codes_register' );
}

function bowe_codes_register_shortcodes() {
	do_action( 'bowe_codes_register_shortcodes' );
}

function bowe_codes_enqueue_scripts() {
	do_action( 'bowe_codes_enqueue_scripts' );
}

function bowe_codes_admin_init() {
	do_action( 'bowe_codes_admin_init' );
}

function bowe_codes_widgets_init() {
	do_action( 'bowe_codes_widgets_init' );
}

/** 
 * Transforms a filter into an action now that member(s) are using templates
 * 
 * @since  2.5
 * 
 * @param  array $backpat some useful vars to load the correct action
 * @uses   has_filter() to check for an eventual filter
 * @uses   apply_filters() to apply it if found
 */
function bowe_codes_backpat_html_member( $backpat = array() ) {

	if( empty( $backpat['bc'] ) )
		return;

	$user_id = ! empty( $backpat['user_id'] ) ? intval( $backpat['user_id'] ) : 0;
	$featured = ! empty( $backpat['featured'] ) ? true : false;
	$doaction = ! empty( $backpat['doaction'] ) ? $backpat['doaction'] : false;

	switch( $backpat['bc'] ) {

		case 'bc_member' :
			if( has_filter( 'bowe_codes_html_member') && !empty( $user_id ) && 'bowe_codes_member_loop_after_fields' == $doaction ) {
				_deprecated_function( 'apply_filters( \'bowe_codes_html_member\' )', '2.5', 'do_action( \'bowe_codes_member_loop_after_fields\' )' );
				echo apply_filters( 'bowe_codes_html_member', '', $user_id );
			}
			if( has_filter( 'bowe_codes_html_member_before_title') && !empty( $user_id ) && 'bowe_codes_member_loop_before_name' == $doaction ) {
				_deprecated_function( 'apply_filters( \'bowe_codes_html_member_before_title\' )', '2.5', 'do_action( \'bowe_codes_member_loop_before_name\' )' );
				echo apply_filters( 'bowe_codes_html_member_before_title', '', $user_id );
			}
			if( has_filter( 'bowe_codes_html_member_after_title') && !empty( $user_id ) && 'bowe_codes_member_loop_after_name' == $doaction ) {
				_deprecated_function( 'apply_filters( \'bowe_codes_html_member_after_title\' )', '2.5', 'do_action( \'bowe_codes_member_loop_after_name\' )' );
				echo apply_filters( 'bowe_codes_html_member_after_title', '', $user_id );
			}
			break;

		case 'bc_members' :
		case 'bc_friends' :
			if( has_filter( 'bowe_codes_members_tag_featured' ) && !empty( $featured ) && 'bowe_codes_member_loop_after_fields' == $doaction ) {
				_deprecated_function( 'apply_filters( \'bowe_codes_members_tag_featured\' )', '2.5', 'do_action( \'bowe_codes_member_loop_after_fields\' )' );
				echo apply_filters( 'bowe_codes_members_tag_featured', '', $user_id );
			}
				
			if( has_filter( 'bowe_codes_members_tag') && empty( $featured ) && 'bowe_codes_member_loop_after_fields' == $doaction ) {
				_deprecated_function( 'apply_filters( \'bowe_codes_members_tag\' )', '2.5', 'do_action( \'bowe_codes_member_loop_after_fields\' )' );
				echo apply_filters( 'bowe_codes_members_tag', '', $user_id );
			}
				
			break;

		case 'bc_group_users' :
			if( has_filter( 'bowe_codes_html_member') && 'bowe_codes_group_member_loop_after_content' == $doaction ) {
				_deprecated_function( 'apply_filters( \'bowe_codes_html_member\' )', '2.5', 'do_action( \'bowe_codes_group_member_loop_after_content\' )' );
				echo apply_filters( 'bowe_codes_html_member', '' );
			}
			if( has_filter( 'bowe_codes_html_member_before_title') && !empty( $user_id ) && 'bowe_codes_group_member_loop_before_name' == $doaction ) {
				_deprecated_function( 'apply_filters( \'bowe_codes_html_member_before_title\' )', '2.5', 'do_action( \'bowe_codes_group_member_loop_before_name\' )' );
				echo apply_filters( 'bowe_codes_html_member_before_title', '', $user_id );
			}
			if( has_filter( 'bowe_codes_html_member_after_title') && !empty( $user_id ) && 'bowe_codes_group_member_loop_after_name' == $doaction ) {
				_deprecated_function( 'apply_filters( \'bowe_codes_html_member_after_title\' )', '2.5', 'do_action( \'bowe_codes_group_member_loop_after_name\' )' );
				echo apply_filters( 'bowe_codes_html_member_after_title', '', $user_id );
			}
				
			break;
	}
}

add_action( 'bowe_codes_member_loop_after_fields', 'bowe_codes_backpat_html_member', 1, 1 );
add_action( 'bowe_codes_member_loop_before_name', 'bowe_codes_backpat_html_member', 1, 1 );
add_action( 'bowe_codes_member_loop_after_name', 'bowe_codes_backpat_html_member', 1, 1 );
add_action( 'bowe_codes_group_member_loop_after_content', 'bowe_codes_backpat_html_member', 1, 1 );
add_action( 'bowe_codes_group_member_loop_before_name', 'bowe_codes_backpat_html_member', 1, 1 );
add_action( 'bowe_codes_group_member_loop_after_name', 'bowe_codes_backpat_html_member', 1, 1 );

/** 
 * Transforms a filter into an action now that members are using templates
 * 
 * @since  2.5
 * 
 * @param  array $backpat some useful vars to load the correct action
 * @uses   has_filter() to check for an eventual filter
 * @uses   apply_filters() to apply it if found
 */
function bowe_codes_backpat_html_members( $backpat = array() ) {
	if( empty( $backpat['bc'] ) )
		return;

	$featured = ! empty( $backpat['featured'] ) ? true : false;
	$doaction = ! empty( $backpat['doaction'] ) ? $backpat['doaction'] : false;

	switch( $backpat['bc'] ) {

		case 'bc_members' :
		case 'bc_friends' :

			if( has_filter( 'bowe_codes_members_tag_featured_after_loop' ) && !empty( $featured ) && 'bowe_codes_member_loop_after_content' == $doaction ) {
				_deprecated_function( 'apply_filters( \'bowe_codes_members_tag_featured_after_loop\' )', '2.5', 'do_action( \'bowe_codes_member_loop_after_content\' )' );
				echo apply_filters( 'bowe_codes_members_tag_featured_after_loop', '' );
			}
				
			if( has_filter( 'bowe_codes_members_tag_after_loop') && 'bowe_codes_member_loop_after_content' == $doaction ) {
				_deprecated_function( 'apply_filters( \'bowe_codes_members_tag_after_loop\' )', '2.5', 'do_action( \'bowe_codes_member_loop_after_content\' )' );
				echo apply_filters( 'bowe_codes_members_tag_after_loop', '' );
			}
				
			break;
	}
}

add_action( 'bowe_codes_member_loop_after_content', 'bowe_codes_backpat_html_members', 1, 1 );

/** 
 * Transforms a filter into an action now that group(s) are using templates
 * 
 * @since  2.5
 * 
 * @param  array $backpat some useful vars to load the correct action
 * @uses   has_filter() to check for an eventual filter
 * @uses   apply_filters() to apply it if found
 */
function bowe_codes_backpat_html_group( $backpat = array() ) {

	if( empty( $backpat['bc'] ) )
		return;

	$group_id = ! empty( $backpat['group_id'] ) ? intval( $backpat['group_id'] ) : 0;
	$featured = ! empty( $backpat['featured'] ) ? true : false;
	$doaction = ! empty( $backpat['doaction'] ) ? $backpat['doaction'] : false;

	switch( $backpat['bc'] ) {

		case 'bc_group' :
			if( has_filter( 'bowe_codes_html_group') && !empty( $group_id ) && 'bowe_codes_group_loop_after_description' == $doaction ) {
				_deprecated_function( 'apply_filters( \'bowe_codes_html_member\' )', '2.5', 'do_action( \'bowe_codes_group_loop_after_description\' )' );
				echo apply_filters( 'bowe_codes_html_group', '', $group_id );
			}
			if( has_filter( 'bowe_codes_html_group_before_title') && !empty( $group_id ) && 'bowe_codes_group_loop_before_name' == $doaction ) {
				_deprecated_function( 'apply_filters( \'bowe_codes_html_group_before_title\' )', '2.5', 'do_action( \'bowe_codes_group_loop_before_name\' )' );
				echo apply_filters( 'bowe_codes_html_group_before_title', '', $group_id );
			}
			if( has_filter( 'bowe_codes_html_group_after_title') && !empty( $group_id ) && 'bowe_codes_group_loop_after_name' == $doaction ) {
				_deprecated_function( 'apply_filters( \'bowe_codes_html_group_after_title\' )', '2.5', 'do_action( \'bowe_codes_group_loop_after_name\' )' );
				echo apply_filters( 'bowe_codes_html_group_after_title', '', $group_id );
			}

		case 'bc_groups' :
		case 'bc_user_groups' :
			if( has_filter( 'bowe_codes_groups_tag_featured' ) && !empty( $featured ) && 'bowe_codes_group_loop_after_name' == $doaction ) {
				_deprecated_function( 'apply_filters( \'bowe_codes_groups_tag_featured\' )', '2.5', 'do_action( \'bowe_codes_group_loop_after_name\' )' );
				echo apply_filters( 'bowe_codes_groups_tag_featured', '' );
			}
				
			if( has_filter( 'bowe_codes_groups_tag') && empty( $featured ) && 'bowe_codes_group_loop_after_name' == $doaction ) {
				_deprecated_function( 'apply_filters( \'bowe_codes_groups_tag\' )', '2.5', 'do_action( \'bowe_codes_group_loop_after_name\' )' );
				echo apply_filters( 'bowe_codes_groups_tag', '', $group_id );
			}
				
			break;
	}
}

add_action( 'bowe_codes_group_loop_after_description', 'bowe_codes_backpat_html_group', 1, 1 );
add_action( 'bowe_codes_group_loop_before_name', 'bowe_codes_backpat_html_group', 1, 1 );
add_action( 'bowe_codes_group_loop_after_name', 'bowe_codes_backpat_html_group', 1, 1 );

/** 
 * Transforms a filter into an action now that groups are using templates
 * 
 * @since  2.5
 * 
 * @param  array $backpat some useful vars to load the correct action
 * @uses   has_filter() to check for an eventual filter
 * @uses   apply_filters() to apply it if found
 */
function bowe_codes_backpat_html_groups( $backpat = array() ) {

	if( empty( $backpat['bc'] ) )
		return;

	$group_id = ! empty( $backpat['group_id'] ) ? intval( $backpat['group_id'] ) : 0;
	$featured = ! empty( $backpat['featured'] ) ? true : false;
	$doaction = ! empty( $backpat['doaction'] ) ? $backpat['doaction'] : false;

	switch( $backpat['bc'] ) {

		case 'bc_groups' :
		case 'bc_user_groups' :
			if( has_filter( 'bowe_codes_groups_tag_featured_after_loop' ) && !empty( $featured ) && 'bowe_codes_group_loop_after_content' == $doaction ) {
				_deprecated_function( 'apply_filters( \'bowe_codes_groups_tag_featured_after_loop\' )', '2.5', 'do_action( \'bowe_codes_group_loop_after_content\' )' );
				echo apply_filters( 'bowe_codes_groups_tag_featured_after_loop', '' );
			}
				
			if( has_filter( 'bowe_codes_groups_tag_after_loop') && 'bowe_codes_group_loop_after_content' == $doaction ) {
				_deprecated_function( 'apply_filters( \'bowe_codes_groups_tag_after_loop\' )', '2.5', 'do_action( \'bowe_codes_group_loop_after_content\' )' );
				echo apply_filters( 'bowe_codes_groups_tag_after_loop', '' );
			}
				
			break;
	}
}

add_action( 'bowe_codes_group_loop_after_content', 'bowe_codes_backpat_html_groups', 1, 1 );

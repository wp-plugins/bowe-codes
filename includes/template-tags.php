<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/* bc_member && bc_members bowe codes template functions */

/**
 * Echoes class for the list of members
 * 
 * @since  2.5
 * 
 * @param  string $suffix
 * @uses   bowe_codes_get_members_loop_class() to get it
 */
function bowe_codes_members_loop_class( $suffix = '' ) {
	echo bowe_codes_get_members_loop_class( $suffix );
}
	
	/**
	 * Returns a class attributes for the members list
	 * 
	 * @since  2.5
	 * 
	 * @global BP_Core_Members_Template $members_template
	 * @param  string $suffix
	 * @uses   apply_filters() to let plugins/themes add their own class
	 * @return string the class attribute
	 */
	function bowe_codes_get_members_loop_class( $suffix = '' ) {
		global $members_template;

		if( empty( $members_template->bowe_codes ) )
			return false;

		$class = !empty( $suffix ) ? $members_template->bowe_codes->class .'-'.$suffix : $members_template->bowe_codes->class;

		$classes = apply_filters( 'bowe_codes_get_members_loop_class', array( $class ), $suffix );

		$retval  = 'class="' . join( ' ', $classes ) . '"';

		return $retval; 
	}

/**
 * Echoes a row class for the member entry
 * 
 * @since  2.5
 * 
 * @uses   bowe_codes_get_member_class() to get it
 */
function bowe_codes_member_class() {
	echo bowe_codes_get_member_class();
}
	
	/**
	 * Returns a row class attributes for the member
	 * 
	 * @since  2.5
	 * 
	 * @global BP_Core_Members_Template $members_template
	 * @uses   apply_filters() to let plugins/themes add their own class
	 * @return string the class attribute
	 */
	function bowe_codes_get_member_class() {
		global $members_template;

		$class = false;

		if( empty( $members_template->bowe_codes ) )
			return false;

		if ( !empty( $members_template->member->featured ) )
			$class = array( 'featured' );

		$classes = apply_filters( 'bowe_codes_get_member_class', $class, $members_template->bowe_codes->class );

		$retval  = !empty( $classes ) ? 'class="' . join( ' ', $classes ) . '"' : false ;

		return $retval;
	}

/**
 * Should we display the member avatar ?
 * 
 * @since  2.5
 * 
 * @global BP_Core_Members_Template $members_template
 * @return boolean true|false
 */
function bowe_codes_member_loop_show_avatar(){
	global $members_template;

	if( empty( $members_template->bowe_codes ) )
		return false;

	$show_avatar = !empty( $members_template->bowe_codes->avatar ) ? $members_template->bowe_codes->avatar : false;

	return $show_avatar;
}

/**
 * Echoes the member avatar
 * 
 * @since  2.5
 * 
 * @uses bowe_codes_get_member_loop_avatar() to get it
 */
function bowe_codes_member_loop_avatar() {
	echo bowe_codes_get_member_loop_avatar();
}

	/**
	 * Returns the member's avatar
	 * 
	 * @since  2.5
	 * 
	 * @global BP_Core_Members_Template $members_template
	 * @return string the avatar
	 */
	function bowe_codes_get_member_loop_avatar() {
		global $members_template;

		if( empty( $members_template->bowe_codes ) )
			return false;

		$avatar_size = !empty( $members_template->bowe_codes->size ) ? $members_template->bowe_codes->size : 50;

		return bp_get_member_avatar( array( 'type' => 'full', 'width' => $avatar_size, 'height' => $avatar_size ) );
	}

/**
 * Should we display profile fields ?
 * 
 * @since  2.5
 * 
 * @global BP_Core_Members_Template $members_template
 * @uses bp_is_active() to check for xprofile component
 * @return mixed comma separated list of profile fields or false
 */
function bowe_codes_member_loop_show_xprofile() {
	global $members_template;

	if( empty( $members_template->bowe_codes ) )
		return false;

	$show_xprofile = !empty( $members_template->bowe_codes->fields ) && bp_is_active( 'xprofile' ) ? $members_template->bowe_codes->fields : false;

	return $show_xprofile;
}

/**
 * Locates and displays the xprofile template
 * 
 * @since  2.5
 * 
 * @global BP_XProfile_Data_Template $profile_template
 * @uses   remove_filter() to temporarly remove some filters
 * @uses   bp_get_template_part() to locate and display the template
 * @uses   add_filter() to restore them
 */
function bowe_codes_member_loop_load_xprofile() {
	global $profile_template;

	// catching profile template as we are about to change it
	$cached_profile_template = $profile_template;

	/* temporarly remove unwanted filters */
	remove_filter( 'bp_get_the_profile_field_value', 'wpautop' );
	remove_filter( 'bp_get_the_profile_field_value', 'xprofile_filter_link_profile_data', 9, 2 );

	/* gets the xprofile template part */
	bp_get_template_part( 'bowecodes', 'xprofile' );

	/* restoring unwanted filters */
	add_filter( 'bp_get_the_profile_field_value', 'wpautop' );
	add_filter( 'bp_get_the_profile_field_value', 'xprofile_filter_link_profile_data', 9, 2 );

	// restoring profile template from cached one
	$profile_template = $cached_profile_template;
}

/**
 * Builds the arguments of the profile loop
 * 
 * @since  2.5
 * 
 * @uses   bowe_codes_member_loop_show_xprofile() to get the desired profile fields
 * @uses   bowe_codes_include_fields() to calculate the profile fields to include by building an exclude list
 * @uses   bp_get_member_user_id() to get the current user id in the members loop
 * @return array the argument of the profil loop
 */
function bowe_codes_member_loop_xprofile_args() {
	$fields = bowe_codes_member_loop_show_xprofile();

	if( empty( $fields) )
		return false;

	$parse_fields = explode(',',$fields);
	$user_xprofile =  bowe_codes_include_fields( $parse_fields );

	return array( 'user_id' => bp_get_member_user_id(), 'exclude_fields' => $user_xprofile );
}

/**
 * Should we display label for xprofile fields ?
 *
 * @since  2.5
 *
 * @global BP_Core_Members_Template $members_template
 * @uses bp_is_active() to check for xprofile component
 * @return boolean true|false
 */
function bowe_codes_member_loop_show_xprofile_label() {
	global $members_template;

	if( empty( $members_template->bowe_codes ) )
		return false;

	$show_xprofile_label = !empty( $members_template->bowe_codes->show_labels ) && bp_is_active( 'xprofile' ) ? $members_template->bowe_codes->show_labels : false;

	return $show_xprofile_label;
}

/* Specific to bc_group_users */

/**
 * Displays some content before the loop
 * 
 * @since  2.5
 * 
 * @uses   remove_filter() to temporarly remove a filter
 * @uses   apply_filters() to benefit from the filters applied to the group descripion
 * @uses   bowe_codes_get_group_members_loop_pre_content() to get the content to add
 * @uses   add_filter() to restore them
 */
function bowe_codes_group_members_loop_pre_content() {
	/* temporarly remove unwanted filters */
	remove_filter( 'bp_get_group_description_excerpt', 'wpautop' );

	echo apply_filters( 'bp_get_group_description_excerpt', bowe_codes_get_group_members_loop_pre_content() );

	/* restoring unwanted filters */
	add_filter( 'bp_get_group_description_excerpt', 'wpautop' );
}

	/**
	 * Displays some content before the loop
	 * 
	 * @since  2.5
	 * 
	 * @global BP_Core_Members_Template $members_template
	 * @uses   apply_filters() to let plugins/themes add their own content
	 */
	function bowe_codes_get_group_members_loop_pre_content() {
		global $members_template;

		if( empty( $members_template->bowe_codes ) )
			return false;

		$show_pre_content = !empty( $members_template->bowe_codes->content ) ? $members_template->bowe_codes->content : false;

		return apply_filters( 'bowe_codes_get_group_members_loop_pre_content', $show_pre_content, $members_template->bowe_codes );
	}

/* Extra data for backward compatibility with old filters */

/**
 * Builds an array of vars useful in order to transform old filters to actions
 * 
 * @since  2.5
 * 
 * @global BP_Core_Members_Template $members_template
 * @param  string $doaction the action that is about to be fired
 * @return array
 */
function bowe_codes_member_loop_backpat( $doaction = '' ) {
	global $members_template;

	if( empty( $members_template->bowe_codes ) )
		return array();

	$backpat = !empty( $members_template->bowe_codes->backpat ) ? $members_template->bowe_codes->backpat : array();

	if( !empty( $members_template->member->id ) )
		$backpat['user_id'] = intval( $members_template->member->id );

	if( !empty( $members_template->member->featured ) )
		$backpat['featured'] = true;

	$backpat['doaction'] = $doaction;

	return $backpat;
}

/* bc_group, bc_groups && bc_user_groups */

/**
 * Displays some content before the loop
 * 
 * @since  2.5
 * 
 * @uses   remove_filter() to temporarly remove a filter
 * @uses   apply_filters() to benefit from the filters applied to the group descripion
 * @uses   bowe_codes_get_groups_loop_pre_content() to get the content to add
 * @uses   add_filter() to restore them
 */
function bowe_codes_groups_loop_pre_content() {
	/* temporarly remove unwanted filters */
	remove_filter( 'bp_get_group_description_excerpt', 'wpautop' );

	echo apply_filters( 'bp_get_group_description_excerpt', bowe_codes_get_groups_loop_pre_content() );

	/* restoring unwanted filters */
	add_filter( 'bp_get_group_description_excerpt', 'wpautop' );
}

	/**
	 * Displays some content before the loop
	 * 
	 * @since  2.5
	 * 
	 * @global BP_Groups_Template $groups_template
	 * @uses   apply_filters() to let plugins/themes add their own content
	 */
	function bowe_codes_get_groups_loop_pre_content() {
		global $groups_template;

		if( empty( $groups_template->bowe_codes ) )
			return false;

		$show_pre_content = !empty( $groups_template->bowe_codes->content ) ? $groups_template->bowe_codes->content : false;

		return apply_filters( 'bowe_codes_get_groups_loop_pre_content', $show_pre_content, $groups_template->bowe_codes );
	}

/**
 * Echoes class for the list of groups
 * 
 * @since  2.5
 * 
 * @param  string $suffix
 * @uses   bowe_codes_get_groups_loop_class to get it
 */
function bowe_codes_groups_loop_class( $suffix = '' ) {
	echo bowe_codes_get_groups_loop_class( $suffix );
}

	/**
	 * Returns a class attributes for the members list
	 * 
	 * @since  2.5
	 * 
	 * @global BP_Groups_Template $groups_template
	 * @param  string $suffix
	 * @uses   apply_filters() to let plugins/themes add their own class
	 * @return string the class attribute
	 */
	function bowe_codes_get_groups_loop_class( $suffix = '' ) {
		global $groups_template;

		if( empty( $groups_template->bowe_codes ) )
			return false;

		$class = !empty( $suffix ) ? $groups_template->bowe_codes->class .'-'.$suffix : $groups_template->bowe_codes->class;

		$classes = apply_filters( 'bowe_codes_get_groups_loop_class', array( $class ), $suffix );

		$retval  = 'class="' . join( ' ', $classes ) . '"';

		return $retval; 
	}

/**
 * Echoes a row class for the group entry
 * 
 * @since  2.5
 * 
 * @uses   bowe_codes_get_group_class() to get it
 */
function bowe_codes_group_class() {
	echo bowe_codes_get_group_class();
}

	/**
	 * Returns a row class attributes for the group
	 * 
	 * @since  2.5
	 * 
	 * @global BP_Groups_Template $groups_template
	 * @uses   apply_filters() to let plugins/themes add their own class
	 * @return string the class attribute
	 */
	function bowe_codes_get_group_class() {
		global $groups_template;

		$class = false;

		if( empty( $groups_template->bowe_codes ) )
			return false;

		if ( !empty( $groups_template->group->featured ) )
			$class = array( 'featured' );

		$classes = apply_filters( 'bowe_codes_get_group_class', $class, $groups_template->bowe_codes->class );

		$retval  = !empty( $classes ) ? 'class="' . join( ' ', $classes ) . '"' : false ;

		return $retval;
	}

/**
 * Should we display the group avatar ?
 * 
 * @since  2.5
 * 
 * @global BP_Groups_Template $groups_template
 * @return boolean true|false
 */
function bowe_codes_group_loop_show_avatar(){
	global $groups_template;

	if( empty( $groups_template->bowe_codes ) )
		return false;

	$show_avatar = !empty( $groups_template->bowe_codes->avatar ) ? $groups_template->bowe_codes->avatar : false;

	return $show_avatar;
}

/**
 * Echoes the group avatar
 * 
 * @since  2.5
 * 
 * @uses bowe_codes_get_group_loop_avatar() to get it
 */
function bowe_codes_group_loop_avatar() {
	echo bowe_codes_get_group_loop_avatar();
}
	
	/**
	 * Returns the group's avatar
	 * 
	 * @since  2.5
	 * 
	 * @global BP_Groups_Template $groups_template
	 * @return string the avatar
	 */
	function bowe_codes_get_group_loop_avatar() {
		global $groups_template;

		if( empty( $groups_template->bowe_codes ) )
			return false;


		$avatar_size = !empty( $groups_template->bowe_codes->size ) ? $groups_template->bowe_codes->size : 50;

		return bp_get_group_avatar( array( 'type' => 'full', 'width' => $avatar_size, 'height' => $avatar_size ) );
	}

/**
 * Should we display the group description ?
 * 
 * @since  2.5
 * 
 * @global BP_Groups_Template $groups_template
 * @return boolean true|false
 */
function bowe_codes_group_loop_show_description() {
	global $groups_template;

	if( empty( $groups_template->bowe_codes ) )
		return false;

	$show_description = !empty( $groups_template->bowe_codes->group_description ) ? $groups_template->bowe_codes->group_description : false;

	return $show_description;
}

/* Extra data for backward compatibility with old filters */

/**
 * Builds an array of vars useful in order to transform old filters to actions
 * 
 * @since  2.5
 * 
 * @global BP_Groups_Template $groups_template
 * @param  string $doaction the action that is about to be fired
 * @return array
 */
function bowe_codes_group_loop_backpat( $doaction = '' ) {
	global $groups_template;

	if( empty( $groups_template->bowe_codes ) )
		return array();

	$backpat = !empty( $groups_template->bowe_codes->backpat ) ? $groups_template->bowe_codes->backpat : array();

	if( !empty( $groups_template->group->id ) )
		$backpat['group_id'] = intval( $groups_template->group->id );

	if( !empty( $groups_template->group->featured ) )
		$backpat['featured'] = true;

	$backpat['doaction'] = $doaction;

	return $backpat;
}

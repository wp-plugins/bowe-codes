<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Echoes version of the plugin
 *
 * @uses bowe_codes_get_version()
 * @return int version
 */
function bowe_codes_version() {
	echo bowe_codes_get_version();
}

	/**
	 * Returns version of the plugin
	 *
	 * @uses  bowecodes()
	 * @return int version
	 */
	function bowe_codes_get_version() {
		return bowecodes()->version;
	}


/**
 * Echoes path to the plugin dir
 *
 * @uses bowe_codes_get_plugin_dir()
 * @return string plugin dir
 */
function bowe_codes_plugin_dir() {
	echo bowe_codes_get_plugin_dir();
}

	/**
	 * Returns path to the plugin dir
	 *
	 * @uses  bowecodes()
	 * @return string plugin dir
	 */
	function bowe_codes_get_plugin_dir() {
		return bowecodes()->plugin_dir;
	}


/**
 * Echoes url of the plugin
 *
 * @uses bowe_codes_get_plugin_url()
 * @return string plugin url
 */
function bowe_codes_plugin_url() {
	echo bowe_codes_get_plugin_url();
}

	/**
	 * Returns url of the plugin
	 *
	 * @uses  bowecodes()
	 * @return string plugin url
	 */
	function bowe_codes_get_plugin_url() {
		return bowecodes()->plugin_url;
	}

/**
 * Echoes url of the plugin includes
 *
 * @uses bowe_codes_get_plugin_includes_url()
 * @return string plugin includes url
 */
function bowe_codes_plugin_includes_url() {
	echo bowe_codes_get_plugin_includes_url();
}

	/**
	 * Returns url of the plugin includes
	 *
	 * @uses  bowecodes()
	 * @return string plugin includes url
	 */
	function bowe_codes_get_plugin_includes_url() {
		return bowecodes()->includes_url;
	}

/**
 * Echoes dir of the plugin includes
 *
 * @uses bowe_codes_get_plugin_includes_dir()
 * @return string plugin includes dir
 */
function bowe_codes_plugin_includes_dir() {
	echo bowe_codes_get_plugin_includes_dir();
}

	/**
	 * Returns dir of the plugin includes
	 *
	 * @uses  bowecodes()
	 * @return string plugin includes dir
	 */
	function bowe_codes_get_plugin_includes_dir() {
		return bowecodes()->includes_dir;
	}

/**
 * Adds plugin's templates folder to BuddyPress template stack
 *
 * @since  2.5
 *
 * @param  array $templates the template stack
 * @uses   bowe_codes_get_plugin_dir() to get plugin's directory path
 * @uses   trailingslashit() to add an ending slash to the path
 * @return array            the template stack with plugin's templates folder included
 */
function bowe_codes_add_template_stack( $templates ) {

	$templates[] = trailingslashit( bowe_codes_get_plugin_dir() . 'templates' );

	return $templates;
}



/** Core shortcodes ******************************************************/

/**
 * Trick to return exluded profile fields based on included ones
 *
 * As BuddyPress only allows an exclude args :(
 *
 * @param  array $include the list of profile field to include
 * @global bp the main BuddyPress global
 * @global wpdb WordPress Database class
 * @uses $wpdb::get_col() to query the profile table
 * @return string comma separated list of fields to exclude
 */
function bowe_codes_include_fields( $include = false ) {
	global $bp, $wpdb;

	if( empty( $include ) )
		return false;

	$include = implode( "','", $include );

	$exclude = $wpdb->get_col( "SELECT id FROM {$bp->profile->table_name_fields} WHERE parent_id = 0 AND name NOT IN('".$include."')" );

	if( is_array( $exclude ) && count( $exclude ) >= 1 )
		return implode( ',', $exclude);
	else
		return false;

}

/**
 * Builds the html part for group(s) & member(s) shortcodes
 *
 * @since  2.5
 *
 * @param  string $slug
 * @param  string $name
 * @uses bp_buffer_template_part() to get the template to apply
 * @uses add_filter() to add plugin's folder to templates stack
 * @uses remove_filter() to delete plugin's folder from templates stack
 * @return string html for a single member
 */
function bowe_codes_buffer_template_part( $slug = '', $name = '' ){
	if( empty( $slug ) || empty( $name ) )
		return false;

	$output = '';

	add_filter( 'bp_get_template_stack', 'bowe_codes_add_template_stack', 10, 1 );

	$output = bp_buffer_template_part( $slug, $name, false );

	remove_filter( 'bp_get_template_stack', 'bowe_codes_add_template_stack', 10, 1 );

	return $output;

}

/**
* Builds the html part for a single member
*
* @deprecated 2.5
*
* @param  int user_id
* @param  boolean avatar
* @param  string size
* @param  string fields
* @uses   bp_core_get_core_userdata() to get member's data
* @uses   bowe_codes_member_tag() as a fallback
*/
function bowe_codes_html_member( $user_id, $avatar=false, $size="50", $fields ='' ){
	_deprecated_function( __FUNCTION__, '2.5', 'bowe_codes_member_tag()' );

	$userdata = bp_core_get_core_userdata( $user_id );

	if( empty( $userdata ) )
		return false;

	$args = array(
		'name'   => $userdata->user_login,
		'avatar' => $avatar,
		'size'   => $size,
		'fields' => $fields
	);

	return bowe_codes_member_tag( $args );
}

/**
 * Handling function for bc_member shortcode
 *
 * @global BP_Core_Members_Template $members_template
 * @param  array $args the shortcode arguments
 * @uses   bp_core_get_userid() to get user's id from his login
 * @uses   bp_has_members() to populate the $members_template globals with members matching the args
 * @uses   bowe_codes_buffer_template_part() to build the output
 * @return string the html of the member
 */
function bowe_codes_member_tag( $args = '' ) {
	global $members_template;

	// caching members template
	$cached_members_template = $members_template;

	if ( ! is_array( $args ) ) {
		return false;
	}

	if ( ! isset( $args['fields'] ) ) {
		$args['fields'] = false;
	}

	$user_id = bp_core_get_userid( $args['name'] );

	if ( empty( $user_id ) ) {
		return '<p class="my_noitems_message">' . esc_html__( 'No member found', 'bowe-codes' ) . '</p>';
	}

	if ( empty( $args['class'] ) ) {
		$args['class'] = 'my_member';
	}

	if ( empty( $args['bowecodes_id'] ) ) {
		$args['bowecodes_id'] = 'bc_member';
	}

	// for plugins and themes if they need to use different template parts for this shortcode
	$template_part = apply_filters( 'bowe_codes_member_get_template_part', array( 'slug' => 'bowecodes' , 'name' => 'members' ), $args['bowecodes_id'], $args );

	if ( bp_has_members( array( 'include' => $user_id ) ) ) {
		//attaching bowe codes settings in members_template
		$members_template->bowe_codes              = new stdClass();
		$members_template->bowe_codes->class       = $args['class'];
		$members_template->bowe_codes->avatar      = $args['avatar'];
		$members_template->bowe_codes->size        = $args['size'];
		$members_template->bowe_codes->fields      = $args['fields'];
		$members_template->bowe_codes->show_labels = $args['show_labels'];
		$members_template->bowe_codes->backpat     = array( 'bc' => $args['bowecodes_id'] );

		// adding a filter here so that plugins/themes can attach more datas to members_template
		$members_template->bowe_codes = apply_filters( 'bowe_codes_member_global', $members_template->bowe_codes, $args['bowecodes_id'], $args );

		$html_member_box = bowe_codes_buffer_template_part( $template_part['slug'], $template_part['name'] );
	}

	//restoring members template from cached one
	$members_template = $cached_members_template;

	return $html_member_box;
}

/**
 * Gets user_ids form users logins
 *
 * @param  string $login_list comma separated list of users logins
 * @uses   bp_core_get_userid() to get user's id from his login
 * @return array the list of user ids
 */
function bowe_codes_get_members_by_login( $login_list = '' ) {
	if( empty( $login_list ) )
		return false;

	$user_ids = array();

	foreach( (array) $login_list as $login ) {
		$user_ids[] = bp_core_get_userid( $login );
	}

	if( empty( $user_ids ) || count( $user_ids ) < 1 )
		return false;

	return $user_ids;
}

/**
 * Handling function for bc_members and bc_friends
 *
 * Since 2.0, we exclusively use the members loop
 * In case of featured members, the include argument of the first
 * loop becomes the exclude one of the second loop.
 *
 * @global BP_Core_Members_Template $members_template
 * @param  array $args the shortcode arguments
 * @uses   bowe_codes_get_members_by_login() to get user ids from user logins
 * @uses   apply_filters() to let plugins/themes change the arguments of the members loop
 * @uses   bp_has_members() to populate the $members_template globals with members matching the args
 * @uses   bowe_codes_buffer_template_part() to build the output
 * @uses   bp_displayed_user_id() to get displayed user id (in case of bc_friends)
 * @uses   bp_loggedin_user_id() to get current user id (in case of bc_friends)
 * @return string html part for the list of users
 */
function bowe_codes_members_tag( $args = '' ){
	global $members_template;

	// caching members template
	$cached_members_template = $members_template;

	if ( ! is_array( $args ) ) {
		return false;
	}

	$html_members_box = false;

	if ( empty( $args['bowecodes_id'] ) ) {
		$args['bowecodes_id'] = 'bc_members';
	}

	// for plugins and themes if they need to use different template parts for this shortcode
	$template_part = apply_filters( 'bowe_codes_members_get_template_part', array( 'slug' => 'bowecodes' , 'name' => 'members' ), $args['bowecodes_id'], $args );

	$exclude_members_from_loop = $featured_members = array();

	if ( ! empty( $args['featured'] ) ) {
		$featured_list = explode( ',', $args['featured'] );

		$exclude_members_from_loop = bowe_codes_get_members_by_login( $featured_list );

		if ( ! empty( $exclude_members_from_loop ) && is_array( $exclude_members_from_loop ) && count( $exclude_members_from_loop ) > 0  ) {

			$featured_arg = apply_filters( 'bowe_codes_members_tag_featured_args', array( 'include' => $exclude_members_from_loop, 'max' => $args['amount'] ), $args );

			if ( bp_has_members( $featured_arg ) ){
				//attaching bowe codes settings in members_template
				$members_template->bowe_codes = new stdClass();

				foreach ( $members_template->members as $member_featured ) {
					$member_featured->featured = true;
				}

				//caching it to merge with regular members
				$featured_members = $members_template->members;

				if ( count( $exclude_members_from_loop ) >= $args['amount'] ) {
					$members_template->bowe_codes->class   = $args['class'];
					$members_template->bowe_codes->avatar  = $args['avatar'];
					$members_template->bowe_codes->size    = $args['size'];
					$members_template->bowe_codes->backpat = array( 'bc' => $args['bowecodes_id'] );

					// adding a filter here so that plugins/themes can attach more datas to members_template
					$members_template->bowe_codes = apply_filters( 'bowe_codes_members_featured_global', $members_template->bowe_codes, $args['bowecodes_id'], $args );

					$html_members_box = bowe_codes_buffer_template_part( $template_part['slug'], $template_part['name'] );

					//restoring members template from cached one
					$members_template = $cached_members_template;

					return $html_members_box;
				}
			}
		}
	}

	$user_id = 0;

	if ( isset( $args['friends'] ) && isset( $args['dynamic'] ) && ! empty( $args['dynamic'] ) && bp_is_user() ) {
		$user_id = bp_displayed_user_id();
	} else if ( isset( $args['friends'] ) ) {
		// Display nothing if we can't get a user id
		if ( ! is_user_logged_in() ) {
			return false;
		}

		$user_id = bp_loggedin_user_id();
	}

	$members_arg = array( 'user_id' => $user_id, 'max' => $args['amount'] );

	if ( ! empty( $args['type'] ) ) {
		$members_arg['type'] = $args['type'];
	}

	if ( ! empty( $exclude_members_from_loop ) && is_array( $exclude_members_from_loop ) && count( $exclude_members_from_loop ) > 0 ) {
		$members_arg['exclude'] = $exclude_members_from_loop;
	}

	$members_arg = apply_filters( 'bowe_codes_members_tag_args', $members_arg, $args );

	if ( bp_has_members( $members_arg ) ) {
		//attaching bowe codes settings in members_template
		$members_template->bowe_codes         = new stdClass();
		$members_template->bowe_codes->class  = $args['class'];
		$members_template->bowe_codes->avatar = $args['avatar'];
		$members_template->bowe_codes->size   = $args['size'];

		if ( ! empty( $user_id ) ) {
			$members_template->bowe_codes->backpat = array( 'bc' => 'bc_friends' );
		} else {
			$members_template->bowe_codes->backpat = array( 'bc' => $args['bowecodes_id'] );
		}

		// adding a filter here so that plugins/themes can attach more datas to members_template
		$members_template->bowe_codes = apply_filters( 'bowe_codes_members_global', $members_template->bowe_codes, $args['bowecodes_id'], $args, $members_arg );

		//we need to eventually merge with featured members !
		$members_template->members = array_merge( $featured_members, $members_template->members );
		$members_template->members = array_slice( $members_template->members, 0, $args['amount'] );

		$members_template->member_count = count( $members_template->members );
		$members_template->total_member_count += count( $featured_members );

		$html_members_box = bowe_codes_buffer_template_part( $template_part['slug'], $template_part['name'] );
	}

	//restoring members template from cached one
	$members_template = $cached_members_template;

	return $html_members_box;
}

/** Notifications ******************************************************/

/**
 * Gets the sender id for a given notification
 *
 * @param  string $date_notified
 * @global $wpdb the WordPress database class
 * @uses   buddypress() to get BuddyPress main instance
 * @return int the sender id
 */
function bowe_codes_get_sender( $date_notified ){
	global $wpdb;
	$bp = buddypress();

	return $wpdb->get_var( $wpdb->prepare( "SELECT sender_id FROM {$bp->messages->table_name_messages} WHERE date_sent = %s", $date_notified ) );
}

/**
 * Handling function for bc_notifications shortcode
 *
 * @param  array $args the shortcode arguments
 * @uses   is_user_logged_in() to check the user is logged in
 * @uses   bp_notifications_get_all_notifications_for_user to fetches all the notifications
 * @uses   bp_loggedin_user_id() to get current user id
 * @uses   bp_notifications_get_notifications_for_user() to get the content to output
 * @uses   bp_core_fetch_avatar() to get the avatar of the item
 * @uses   bowe_codes_get_sender() to get the sender id in case of a notification related to a message
 * @return string html for the notifications
 */
function bowe_codes_notifications_tag( $args = '' ){
	if ( ! is_array( $args ) ) {
		return false;
	}

	if ( ! is_user_logged_in() ) {
		return false;
	}

	$notifications = bp_notifications_get_all_notifications_for_user( bp_loggedin_user_id() );
	$notifications_content = bp_notifications_get_notifications_for_user( bp_loggedin_user_id() );

	$html_notifications_box = '<div class="' . $args['class']  .'">';

	if ( ! empty( $notifications_content ) && count( $notifications_content ) > 0 ) {

		$html_notifications_box .= '<ul class="' . $args['class'] . '-ul">';

		for ( $i=0 ;$i < count( $notifications_content ); $i++ ){

			if ( $i < $args['amount'] ) {
				$html_notifications_box .= '<li>';

				if ( ! empty( $args['avatar'] ) ) {
					$html_notifications_box .= '<div class="bc_avatar">';

					$avatar_fetch_args = array(
						'item_id'    => $notifications[ $i ]->item_id,
						'type'       => 'full',
						'width'      => $args['size'],
						'height'     => $args['size']
					);

					if ( 'groups' == $notifications[ $i ]->component_name ) {
						$avatar_fetch_args = array_merge( $avatar_fetch_args, array(
							'object'     => 'group',
							'avatar_dir' => 'group-avatars',
						) );

						if ( $notifications[ $i ]->secondary_item_id != 0 ) {
							$avatar_fetch_args['item_id'] = $notifications[ $i ]->secondary_item_id;
						}

					} else if ( 'messages' == $notifications[ $i ]->component_name ) {
						$avatar_fetch_args['item_id'] = bowe_codes_get_sender( $notifications[$i]->date_notified );
					} else if ( 'new_at_mention' ==  $notifications[ $i ]->component_action ) {
						$avatar_fetch_args['item_id'] = $notifications[ $i ]->secondary_item_id;
					}

					$html_notifications_box .= bp_core_fetch_avatar( $avatar_fetch_args );
					$html_notifications_box .= '</div>';
				}

				$html_notifications_box .= '<div class="notification-infos">';
				$html_notifications_box .= '<p class="bc_notifications">' . $notifications_content[ $i ] .'</p>';
				$html_notifications_box .= '</div></li>';
			}
		}
	} else {
		$html_notifications_box .= '<ul class="' . $args['class'] . '-ul">';
		$html_notifications_box .= '<li><div class="notification-infos">';
		$html_notifications_box .= '<p class="bc_notifications">'. __( 'No new notifications.', 'bowe-codes' ) .'</p>';
		$html_notifications_box .= '</div></li>';
	}

	$html_notifications_box .= '</ul></div>';
	return $html_notifications_box;
}

/** Groups ******************************************************/

/**
 * Builds the html part for a single group
 *
 * @deprecated 2.5
 *
 * @param  int  $id
 * @param  string  $name
 * @param  string  $slug
 * @param  string  $size
 * @param  boolean $avatar
 * @param  string $permalink
 * @param  boolean $desc
 *
 * @uses bowe_codes_group_tag() as a fallback
 */
function bowe_codes_html_group( $id, $name, $slug, $size="50", $avatar = false, $permalink = false, $desc = false ){
	_deprecated_function( __FUNCTION__, '2.5', 'bowe_codes_group_tag()' );

	if ( empty( $slug) ) {
		return;
	}

	$args = array(
		'slug'   => $slug,
		'avatar' => $avatar,
		'size'   => $size,
		'desc'   => $desc
	);

	return bowe_codes_group_tag( $args );
}

/**
 * Handling function for bc_group shortcode
 *
 * @global BP_Groups_Template $groups_template
 * @param  array $args the shortcode arguments
 * @uses   groups_get_id() to get the group id from its slug
 * @uses   bp_has_groups() to populate the $groups_template global with the desired group
 * @uses   bowe_codes_buffer_template_part() to build the output
 * @return string the html part for group
 */
function bowe_codes_group_tag( $args = '' ){
	global $groups_template;
	$bp = buddypress();

	// caching groups template
	$cached_groups_template = $groups_template;

	// on single group we need to temporarly empty the $bp->groups->current_group->slug
	if ( isset( $bp->groups->current_group->slug ) && $bp->groups->current_group->slug ) {
		$cached_current_group = $bp->groups->current_group->slug;
		$bp->groups->current_group->slug = '';
	}

	if ( ! is_array( $args ) ) {
		return false;
	}

	$bc_group_id = groups_get_id( $args['slug'] );

	if ( empty( $bc_group_id ) ) {
		return '<p class="my_noitems_message">' . esc_html__( 'No group found', 'bowe-codes' ) . '</p>';
	}

	if ( empty( $args['class'] ) ) {
		$args['class'] = 'my_group';
	}

	if ( empty( $args['bowecodes_id'] ) ) {
		$args['bowecodes_id'] = 'bc_group';
	}

	// for plugins and themes if they need to use different template parts for this shortcode
	$template_part = apply_filters( 'bowe_codes_group_get_template_part', array( 'slug' => 'bowecodes' , 'name' => 'groups' ), $args['bowecodes_id'], $args );

	$html_group_box = '';

	if ( bp_has_groups( array( 'include' => $bc_group_id ) ) ) {
		//attaching bowe codes settings in groups_template
		$groups_template->bowe_codes                    = new stdClass();
		$groups_template->bowe_codes->class             = $args['class'];
		$groups_template->bowe_codes->avatar            = $args['avatar'];
		$groups_template->bowe_codes->size              = $args['size'];
		$groups_template->bowe_codes->group_description = $args['desc'];
		$groups_template->bowe_codes->backpat           = array( 'bc' => $args['bowecodes_id'] );

		// adding a filter here so that plugins/themes can attach more datas to groups_template
		$groups_template->bowe_codes = apply_filters( 'bowe_codes_group_global', $groups_template->bowe_codes, $args['bowecodes_id'], $args );

		$html_group_box = bowe_codes_buffer_template_part( $template_part['slug'], $template_part['name'] );
	}

	//restoring groups template from cached one
	$groups_template = $cached_groups_template;

	//restoring current group from cached one
	if ( ! empty( $cached_current_group ) ) {
		$bp->groups->current_group->slug = $cached_current_group;
	}

	return $html_group_box;
}

/**
 * Builds an array of group ids from one of group slugs
 *
 * @param  array $slug_list the list of featured group slug
 * @uses groups_get_id() to get the id of a group from its slug
 * @return array the group ids
 */
function bowe_codes_get_groups_by_slug( $slug_list ) {
	$group_ids = array();

	if ( empty( $slug_list ) || ! is_array( $slug_list ) ) {
		return false;
	}

	foreach ( $slug_list as $slug ) {
		$bc_group_id = false;
		$bc_group_id = groups_get_id( $slug );

		if ( ! empty( $bc_group_id ) ) {
			$group_ids[] = $bc_group_id;
		}
	}

	if ( empty( $group_ids ) || count( $group_ids ) < 1 ) {
		return false;
	}

	return $group_ids;
}

/**
 * Handling function for the bc_groups and bc_user_groups shortcodes
 *
 * Since 2.0, we exclusively use the groups loop
 * In case of featured groups, the include argument of the first
 * loop becomes the exclude one of the second loop.
 *
 * @global BP_Groups_Template $groups_template
 * @param  array $args the shortcode arguments
 * @uses   bowe_codes_get_groups_by_slug() to get an array of group ids out of a comma separated list of slugs
 * @uses   apply_filters() to let plugins/themes change the arguments of the loop
 * @uses   bp_has_groups() to populate the $groups_template global with the desired groups
 * @uses   bowe_codes_buffer_template_part() to build the output
 * @uses   bp_is_user() to check for the member profile area
 * @uses   bp_displayed_user_id() to get displayed user id in case of bc_user_groups
 * @uses   bp_loggedin_user_id() to get current user id in case of bc_user_groups
 * @return string the html part for the groups
 */
function bowe_codes_groups_tag( $args = '' ){
	global $groups_template;
	$bp = buddypress();

	// caching groups template
	$cached_groups_template = $groups_template;

	// on single group we need to temporarly empty the $bp->groups->current_group->slug
	if ( isset( $bp->groups->current_group->slug ) && $bp->groups->current_group->slug ) {
		$cached_current_group = $bp->groups->current_group->slug;
		$bp->groups->current_group->slug = '';
	}

	if ( ! is_array( $args ) ) {
		return false;
	}

	if ( empty( $args['bowecodes_id'] ) ) {
		$args['bowecodes_id'] = 'bc_groups';
	}

	// for plugins and themes if they need to use different template parts for this shortcode
	$template_part = apply_filters( 'bowe_codes_groups_get_template_part', array( 'slug' => 'bowecodes' , 'name' => 'groups' ), $args['bowecodes_id'], $args );

	$html_groups_box = '';
	$exclude_groups_from_loop = $featured_groups = array();

	if ( ! empty( $args['featured'] ) ) {

		$featured_list = explode( ',', $args['featured'] );
		$exclude_groups_from_loop = bowe_codes_get_groups_by_slug( $featured_list );

		if ( ! empty( $exclude_groups_from_loop ) && is_array( $exclude_groups_from_loop ) && count( $exclude_groups_from_loop ) > 0 ) {

			$featured_arg = apply_filters( 'bowe_codes_groups_tag_featured_args', array( 'include' => $exclude_groups_from_loop, 'max' => $args['amount'] ), $args );

			if ( bp_has_groups( $featured_arg ) ) {
				//attaching bowe codes settings in groups_template
				$groups_template->bowe_codes = new stdClass();

				foreach ( $groups_template->groups as $group_featured ) {
					$group_featured->featured = true;
				}

				//caching it to merge with regular groups
				$featured_groups = $groups_template->groups;

				if ( count( $exclude_groups_from_loop ) >= $args['amount'] ) {
					$groups_template->bowe_codes->class  = $args['class'];
					$groups_template->bowe_codes->avatar = $args['avatar'];
					$groups_template->bowe_codes->size   = $args['size'];

					if ( ! empty( $args['content'] ) ) {
						$groups_template->bowe_codes->content = $args['content'];
					} else {
						$groups_template->bowe_codes->content = false;
					}

					// adding a filter here so that plugins/themes can attach more datas to groups_template
					$groups_template->bowe_codes = apply_filters( 'bowe_codes_groups_featured_global', $groups_template->bowe_codes, $args['bowecodes_id'], $args );

					$html_groups_box = bowe_codes_buffer_template_part( $template_part['slug'], $template_part['name'] );

					//restoring groups template from cached one
					$groups_template = $cached_groups_template;

					return $html_groups_box;
				}
			}
		}
	}

	$group_args = array( 'type' => $args['type'], 'per_page' => $args['amount'], 'max' => $args['amount'] );

	if ( ! empty( $exclude_groups_from_loop ) && is_array( $exclude_groups_from_loop ) && count( $exclude_groups_from_loop ) > 0 ) {
		$group_args['exclude'] = $exclude_groups_from_loop;
	}

	// faut vérifier ce truc !!
	if ( ! empty( $args['user_groups'] ) ){

		if ( bp_is_user() && $args['dynamic'] ) {
			$user_id = bp_displayed_user_id();
		} else {
			$user_id = bp_loggedin_user_id();
		}

		if ( ! empty( $user_id ) ) {
			$group_args['user_id'] = $user_id;
		} else {
			return $html_groups_box;
		}
	}

	$group_args = apply_filters( 'bowe_codes_groups_tag_args', $group_args, $args );
	$group_filter = 'bowe_codes_groups_global';

	if ( bp_has_groups( $group_args ) ) {
		//attaching bowe codes settings in groups_template
		$groups_template->bowe_codes         = new stdClass();
		$groups_template->bowe_codes->class  = $args['class'];
		$groups_template->bowe_codes->avatar = $args['avatar'];
		$groups_template->bowe_codes->size   = $args['size'];

		if ( ! empty( $args['content'] ) ) {
			$groups_template->bowe_codes->content = $args['content'];
		} else {
			$groups_template->bowe_codes->content = false;
		}

		if ( ! empty( $user_id ) ) {
			$groups_template->bowe_codes->backpat = array( 'bc' => 'bc_user_groups' );
			$group_filter .= '_user_groups';
		} else {
			$groups_template->bowe_codes->backpat = array( 'bc' => $args['bowecodes_id'] );
		}

		// adding a filter here so that plugins/themes can attach more datas to groups_template
		$groups_template->bowe_codes = apply_filters( $group_filter, $groups_template->bowe_codes, $args['bowecodes_id'], $args );

		//we need to eventually merge with featured groups !
		$groups_template->groups = array_merge( $featured_groups, $groups_template->groups );
		$groups_template->groups = array_slice( $groups_template->groups, 0, $args['amount'] );

		$groups_template->group_count = count( $groups_template->groups );
		$groups_template->total_group_count += count( $featured_groups );

		$html_groups_box = bowe_codes_buffer_template_part( $template_part['slug'], $template_part['name'] );

	// Fetch the featured group !
	} else if ( ! empty( $featured_groups ) ) {
		$groups_template->groups            = $featured_groups;
		$groups_template->group_count       = count( $featured_groups );
		$groups_template->total_group_count = $groups_template->group_count;

		//attaching bowe codes settings in groups_template
		$groups_template->bowe_codes         = new stdClass();
		$groups_template->bowe_codes->class  = $args['class'];
		$groups_template->bowe_codes->avatar = $args['avatar'];
		$groups_template->bowe_codes->size   = $args['size'];

		if ( ! empty( $args['content'] ) ) {
			$groups_template->bowe_codes->content = $args['content'];
		} else {
			$groups_template->bowe_codes->content = false;
		}

		// adding a filter here so that plugins/themes can attach more datas to groups_template
		$groups_template->bowe_codes = apply_filters( 'bowe_codes_groups_featured_global', $groups_template->bowe_codes, $args['bowecodes_id'], $args );

		$html_groups_box = bowe_codes_buffer_template_part( $template_part['slug'], $template_part['name'] );

	// Display a message to inform no group were found
	} else {
		$html_groups_box = '<p class="my_noitems_message">' . esc_html__( 'No group found', 'bowe-codes' ) . '</p>';
	}

	//restoring groups template from cached one
	$groups_template = $cached_groups_template;

	//restoring current group from cached one
	if ( ! empty( $cached_current_group ) ) {
		$bp->groups->current_group->slug = $cached_current_group;
	}

	return $html_groups_box;
}

/**
 * Handling function for bc_group_users shortcode
 *
 * @global BP_Core_Members_Template $members_template
 * @param  array $args the shortcode arguments
 * @uses   groups_get_id() to get group id from its slug
 * @uses   bp_group_has_members() to populate the $members_template global with desired group members
 * @uses   bowe_codes_buffer_template_part() to build the output
 * @return string html the list of members for a given group
 */
function bowe_codes_group_users_tag( $args = '' ) {
	global $members_template;

	// caching members template
	$cached_members_template = $members_template;

	if ( ! is_array( $args ) ) {
		return false;
	}

	$bc_group_id = groups_get_id( $args['slug'] );
	$html_members_box = '';

	if ( empty( $bc_group_id ) ) {
		return false;
	}

	if ( empty( $args['bowecodes_id'] ) ) {
		$args['bowecodes_id'] = 'bc_group_users';
	}

	// for plugins and themes if they need to use different template parts for this shortcode
	$template_part = apply_filters( 'bowe_codes_group_users_get_template_part', array( 'slug' => 'bowecodes' , 'name' => 'groupmembers' ), $args['bowecodes_id'], $args );

	$group_users_arg = array(
		'exclude_admins_mods' => 0,
		'group_id'            => $bc_group_id,
		'max'                 => $args['amount'],
		'per_page'            => $args['amount'],
	);

	$group_users_arg = apply_filters( 'bowe_codes_group_users_tag_args', $group_users_arg, $args );

	if ( bp_group_has_members( $group_users_arg ) ) {
		//attaching bowe codes settings in members_template
		$members_template->bowe_codes         = new stdClass();
		$members_template->bowe_codes->class  = $args['class'];
		$members_template->bowe_codes->avatar = $args['avatar'];
		$members_template->bowe_codes->size   = $args['size'];

		if ( ! empty( $args['content'] ) ) {
			$members_template->bowe_codes->content = $args['content'];
		} else {
			$members_template->bowe_codes->content = false;
		}

		$members_template->bowe_codes->backpat = array( 'bc' => $args['bowecodes_id'] );

		// adding a filter here so that plugins/themes can attach more datas to members_template
		$members_template->bowe_codes = apply_filters( 'bowe_codes_group_users_global', $members_template->bowe_codes, $args['bowecodes_id'], $args, $group_users_arg );

		$html_members_box = bowe_codes_buffer_template_part( $template_part['slug'], $template_part['name'] );

	// Display a message to inform no member were found for this group
	} else {
		$html_members_box = '<p class="my_noitems_message">' . esc_html__( 'No member found', 'bowe-codes' ) . '</p>';
	}

	//restoring groups template from cached one
	$members_template = $cached_members_template;

	return $html_members_box;
}

/**
 * Handling function for bc_restrict_gm shortcode
 *
 * @param  array $args the shortcode arguments
 * @uses groups_get_id() to get group id from its slug
 * @uses is_user_logged_in() to check if the user is logged in
 * @uses groups_is_user_member() to check if the user is member of the group
 * @uses groups_get_group() to get the group object
 * @uses bp_get_group_permalink() to build the link to the group
 * @return string the restricted content or a warning message
 */
function bowe_code_hide_post_content_tag( $args = '' ) {
	if ( ! is_array( $args ) ) {
		return false;
	}

	$bc_group_id = false;

	// if no group id content is return
	if ( empty( $args['group_id'] ) ) {
		return __( 'No group id or group slug were given', 'bowe-codes' );
	}

	if ( is_numeric( $args['group_id'] ) ) {
		$bc_group_id = $args['group_id'];
	} else {
		$bc_group_id = groups_get_id( $args['group_id'] );
	}

	if ( empty( $bc_group_id ) ) {
		return __( 'The group slug given is unknown', 'bowe-codes' );
	}

	// if user not logeddin, he's asked to
	if( ! is_user_logged_in() ) {
		$message_unlogged = '<p class="' . $args['class'] . '">' . esc_html__('You must be loggedin to access to this content', 'bowe-codes') . '</p>';
		return apply_filters( 'bowe_code_hide_post_unconnect_message', $message_unlogged );
	}

	$user_id = bp_loggedin_user_id();

	// if the user is a group member, let's return the content
	if ( groups_is_user_member( $user_id, $bc_group_id ) ) {
		return $args['content'];
	} else {
		$group = groups_get_group( array( 'group_id' => $bc_group_id ) );
		$message_notgm = '<p class="' . $args['class'] . '">' . sprintf( __('You must be a member of the group %s to access this content', 'bowe-codes'), '<a href="' . bp_get_group_permalink( $group ) . '">' . $group->name. '</a>' ) . '</p>';

		return apply_filters( 'bowe_code_hide_post_connect_message', $message_notgm, $bc_group_id );
	}
}

/** Messages ******************************************************/

/**
 * Handling function for the shortcode bc_messages
 *
 * @param  array $args the shortcode arguments
 * @global messages_template
 * @uses is_user_logged_in() to check for loggedin user
 * @uses  the messages loop
 * @uses bp_core_get_user_domain() to get member's home page link
 * @uses bp_core_get_user_displayname() to get member's display name
 * @uses bp_core_fetch_avatar() to get member's avatar
 * @uses bp_create_excerpt() to truncate the message content
 * @return string the list of messages
 */
function bowe_codes_messages_tag( $args = '' ){
	global $messages_template;

	if ( ! is_array( $args ) ) {
		return false;
	}

	if ( ! is_user_logged_in() ) {
		return false;
	}

	$html_messages_box = '<div class="'. $args['class'] . '">';

	$message_args = array(
		'box'      => 'inbox',
		'per_page' => $args['amount'],
	);

	if ( ! empty( $args['type'] ) ) {
		$message_args['type'] = $args['type'];
	}

	if( bp_has_message_threads( $message_args ) ){

		$html_messages_box .='<ul class="' . $args['class'] . '-ul">';

		while ( bp_message_threads() ){
			bp_message_thread();
			$html_messages_box .= '<li>';
			$sender_home = bp_core_get_user_domain( $messages_template->thread->last_sender_id );

			if ( ! empty( $args['avatar'] ) ) {
				$html_messages_box .= '<div class="bc_avatar"><a href="' . $sender_home . '" title="' . esc_html__( 'From:', 'bowe-codes' ) . ' ' . bp_core_get_user_displayname( $messages_template->thread->last_sender_id ) . '">' . bp_core_fetch_avatar( array( 'item_id' => $messages_template->thread->last_sender_id, 'type' => 'full', 'width' => $args['size'], 'height' => $args['size'] ) ) . '</a></div>';
			}

			$html_messages_box .= '<div class="message-infos">';

			if ( empty( $args['avatar'] ) && ! empty( $args['subject'] ) ) {
				$html_messages_box .= '<span class="bc_from">' . esc_html__( 'From:','bowe-codes' ) . ' ' . bp_get_message_thread_from() . ' </span>';
			} else if ( empty( $args['avatar'] ) && empty( $args['subject'] ) ) {
				$html_messages_box .= '<span class="bc_from">' . esc_html__( 'From:', 'bowe-codes' ) . '<a href="' . bp_get_message_thread_view_link() . '" title="' . esc_attr__( 'View Message', 'bowe-codes' ) . '">' . bp_core_get_user_displayname( $messages_template->thread->last_sender_id ) . '</a>';
			} else if( ! empty( $args['subject'] ) ) {
				$html_messages_box .= '<span class="bc_subject"><a href="' . bp_get_message_thread_view_link() . '" title="' . esc_attr__( 'View Message', 'bowe-codes' ) . '">' . bp_get_message_thread_subject() . '</a></span>';
			}

			$html_messages_box .= '<p class="bc_excerpt">'. strip_tags( bp_create_excerpt( $messages_template->thread->last_message_content, intval( $args['excerpt'] ) ) ).'</p>';
			$html_messages_box .= '</div></li>';
		}
	}
	$html_messages_box .='</ul></div>';
	return $html_messages_box;
}

/** Blogs ******************************************************/

/**
 * Builds the html part for a single blog
 *
 * @param  int $blog_id
 * @param  boolean $avatar
 * @param  string $size
 * @param  boolean $desc
 * @uses get_blog_option()
 * @uses get_avatar() to fetch blog's administrator avatar
 * @return string the blog html part
 */
function bowe_codes_html_blog( $blog_id, $avatar, $size, $desc ){
	$blog_html = '';
	$site_url = get_blog_option( $blog_id, 'siteurl' );

	if ( ! empty( $avatar ) ) {
		$blog_html .= '<div class="bc_avatar"><a href="' . esc_url( $site_url ) . '">'. get_avatar( get_blog_option( $blog_id, 'admin_email' ), $size ) . '</a></div>';
	}

	$blog_html .= '<div class="blog-infos">';
	$blog_html .= '<h4><a href="' . esc_url( $site_url ) . '">' . esc_html( get_blog_option( $blog_id, 'blogname' ) ) . '</a></h4>';

	if( !empty( $desc ) )
		$blog_html .= '<p>' . esc_html( get_blog_option( $blog_id, 'blogdescription' ) ) . '</p>';

	$blog_html .= '</div>';

	return $blog_html;
}

/**
 * Handling function for bc_blogs shortcode
 *
 * @param  array $args the shortcode arguments
 * @global blog_id
 * @global blogs_template
 * @uses bp_get_root_blog_id() to get the blog id BuddyPress is running on
 * @uses bowe_codes_html_blog() to get a single blog html part (in case of featured blog)
 * @uses the blogs loop
 * @return string html of the list of blogs
 */
function bowe_codes_blogs_tag( $args = '' ){
	global $blog_id, $blogs_template;

	// not available for children !
	if ( $blog_id != bp_get_root_blog_id() ) {
		return false;
	}

	if ( ! is_array( $args ) ) {
		return false;
	}

	$html_blogs_box = '<div class="' . $args['class'] . '">';
	$exclude_blogs_from_loop = array();

	if ( ! empty( $args['featured'] ) ){

		$featured_list = explode( ',', $args['featured'] );

		if ( count( $featured_list ) > 0 ){

			$html_blogs_box .= '<div class="featured">';
			$html_blogs_box .='<ul class="' . $args['class'] . '-ul">';

			foreach ( $featured_list as $feat_ids ){
				$exclude_blogs_from_loop[] = $feat_ids;
				$html_blogs_box .= '<li> '. bowe_codes_html_blog( $feat_ids, $args['avatar'], $args['size'], $args['desc'] ) . '</li>';
			}
			$html_blogs_box .='</ul></div>';
		}

		if ( count( $featured_list ) == $args['amount'] ){
			return $html_blogs_box . '</div>';
		}
	}

	$blogs_args = apply_filters( 'bowe_codes_blogs_tag_args', array(
		'user_id'  => false,           // make sure to list all blogs even if seeing a single user
		'type'     => $args['type'],
		'per_page' => $args['amount'],
		'max'      => $args['amount']
	) );

	if ( bp_has_blogs( $blogs_args ) ) {

		$html_blogs_box .= '<ul class="' . $args['class'] . '-ul">';
		$i=1;
		$j=0;
		while ( bp_blogs() ){
			bp_the_blog();

			$check = $args['amount'] - count( $exclude_blogs_from_loop );

			if ( isset( $exclude_blogs_from_loop ) && in_array( $blogs_template->blogs[ $j ]->blog_id, $exclude_blogs_from_loop ) ) {
				$i-=1;
			} else if ( $i <= $args['amount'] - count( $exclude_blogs_from_loop ) ) {
				if ( ! empty( $args['avatar'] ) ) {
					$html_blogs_box .= '<li><div class="bc_avatar"><a href="' . bp_get_blog_permalink() . '">' . bp_get_blog_avatar( array( 'width' => $args['size'], 'height' => $args['size'] ) ) . '</a></div>';
				} else {
					$html_blogs_box .= '<li>';
				}

				$html_blogs_box .= '<div class="blog-infos">';
				$html_blogs_box .= '<h4><a href="' . bp_get_blog_permalink() . '">' . bp_get_blog_name() . '</a></h4>';

				if ( ! empty( $args['desc'] ) ) {
					$html_blogs_box .= '<p>' . bp_get_blog_description() . '</p>';
				}

				$html_blogs_box .= '</div></li>';
			}

			$i+=1;
			$j+=1;
		}
	}

	$html_blogs_box .='</ul></div>';
	return $html_blogs_box;
}

/** Activities ******************************************************/

/**
 * Gets the activity types after checking 1.7 function exists
 *
 * @since  2.0.1
 *
 * @uses bp_is_active to check for activity component
 * @uses bp_activity_get_types() to return the available activity types.
 * @return array the list of activities or warning message
 */
function bowe_codes_activity_list_types() {
	if ( bp_is_active( 'activity' ) && function_exists( 'bp_activity_get_types' ) ) {
		return bp_activity_get_types();
	} else {
		return array( 'activity_update' => 'BuddyPress 1.7 is required' );
	}
}

/**
 * Handling function for the bc_activity shortcode
 *
 * @param  array $args the shortcode arguments
 * @uses the activity loop
 * @return string html for the list of activities
 */
function bowe_codes_activity_tag( $args = '' ) {
	if ( ! is_array( $args ) ) {
		return false;
	}

	$html_activity_box = '<div class="' . $args['class'] . '">';

	$activity_args = array(
		'action'   => $args['type'],
		'max'      => $args['amount'],
		'per_page' => $args['amount']
	);

	$activity_args = apply_filters( 'bowe_codes_activity_tag_arg', $activity_args, $args );

	if( bp_has_activities( $activity_args ) ) {

		$html_activity_box .= '<ul class="' . $args['class'] . '-ul">';

		while ( bp_activities() ) {
			bp_the_activity();

			$html_activity_box .= '<li>';

			if ( ! empty( $args['avatar'] ) ) {
				$html_activity_box .= '<div class="bc_avatar"><a href="' . bp_get_activity_user_link() . '">';
				$html_activity_box .= bp_core_fetch_avatar( array(
					'item_id' => bp_get_activity_user_id(),
					'type'    => 'full',
					'width'   => $args['size'],
					'height'  => $args['size'],
				) );

				$html_activity_box .= '</a></div>';
			}

			$html_activity_box .= '<div class="activity-infos">';
			$html_activity_box .= apply_filters( 'bowe_codes_activity_before_action', '' );
			$html_activity_box .= '<div class="activity-action">'. bp_get_activity_action() .'</div>';
			$html_activity_box .= apply_filters( 'bowe_codes_activity_before_content', '' );
			$html_activity_box .= '<div class="activity-content">'. bp_get_activity_content_body() .'</div>';
			$html_activity_box .= apply_filters( 'bowe_codes_activity_after_content', '' );
			$html_activity_box .= '</div></li>';
		}
		$html_activity_box .= '</ul>';
	}

	$html_activity_box .= '</div>';

	return $html_activity_box;
}

<?php

/**
 * Plugin Name: WP User Preferences
 * Plugin URI:  https://wordpress.org/plugins/wp-user-preferences/
 * Description: Prefer user settings over site & network settings
 * Author:      John James Jacoby
 * Version:     0.1.0
 * Author URI:  https://profiles.wordpress.org/johnjamesjacoby/
 * License:     GPL v2 or later
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wp_get_current_user_preference' ) ) :
/**
 * Get preference for the currently logged in user. If no user is logged in,
 * a site or network preference will be returned if one is set.
 *
 * @since 0.1.0
 *
 * @param  string  $key
 * @return mixed
 */
function wp_get_current_user_preference( $key = '' ) {
	return wp_get_user_preference( get_current_user_id(), $key );
}
endif;

if ( ! function_exists( 'wp_get_user_preference' ) ) :
/**
 * Get a user preference
 *
 * @since 0.1.0
 *
 * @param  int     $user_id
 * @param  string  $key
 *
 * @return mixed
 */
function wp_get_user_preference( $user_id = 0, $key = '' ) {

	// Get user/site/network preference map
	$keys = wp_map_user_preference_key( $key );

	// Check usermeta first
	$retval = get_usermeta( $user_id, $keys['user'] );

	// Nothing, so check site option
	if ( false === $retval ) {
		$retval = get_option( $keys['site'] );

		// Nothing, so check network option if multisite
		if ( false === $retval && is_multisite() ) {
			$retval = get_site_option( $keys['network'] );
		}
	}

	// Filter & return
	return apply_filters( 'wp_get_user_preference', $retval, $user_id, $key );
}
endif;

if ( ! function_exists( 'wp_map_user_preference_key' ) ) :
/**
 * Return an array of key/value pairs for user/site/network settings, based on
 * the usermeta key being passed in.
 *
 * This function exists because not all user, site, & network metadata keys are
 * the same, even though they return the same type of data back. By passing in
 * the usermeta key, we can make decisions about what site & network keys map to
 * a user preference, and fallback to them if no user preference is set yet.
 *
 * @since 0.1.0
 *
 * @param   string  $key
 *
 * @return  array
 */
function wp_map_user_preference_key( $key = '' ) {

	// Which usermeta key are we mapping to?
	switch ( $key ) {

		// These keys are different between user/site/network
		case 'some_other_preference' :
			$retval = array(
				'user'    => 'one',
				'site'    => 'two',
				'network' => 'three'
			);
			break;

		// These keys are the same between user/site/network
		case 'admin_color' :
		case 'timezone' :
		case 'time_format' :
		case 'date_format' :
		case 'WPLANG' :
		default :

			// Default return value
			$retval = array(
				'user'    => $key,
				'site'    => $key,
				'network' => $key
			);

			break;
	}

	// Filter & return
	return apply_filters( 'wp_map_user_preference_key', $retval, $key );
}
endif;

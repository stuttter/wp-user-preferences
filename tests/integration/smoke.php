<?php

/**
 * Exercise the user, site, and network preference fallback contract.
 *
 * This file is loaded by the centrally maintained integration runner after the
 * production plugin build has been activated.
 *
 * @package WP_User_PreferencesTests
 */

defined( 'ABSPATH' ) || exit;

$assert = static function ( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
};

$user_id    = 0;
$key        = 'wpup_smoke_' . strtolower( wp_generate_password( 12, false, false ) );
$site_value = 'site-value';
$network_value = 'network-value';

try {
	$assert( is_multisite(), 'WP User Preferences must use the multisite integration profile.' );
	$assert( function_exists( 'wp_get_user_preference' ), 'The production plugin did not load.' );

	$created_user_id = wp_insert_user(
		array(
			'user_login' => 'portfolio-preference-' . strtolower( wp_generate_password( 12, false, false ) ),
			'user_pass'  => wp_generate_password( 24, true, true ),
			'user_email' => 'preference-' . wp_generate_uuid4() . '@example.test',
			'role'       => 'subscriber',
		)
	);
	$assert( ! is_wp_error( $created_user_id ), 'WordPress could not create the smoke-test user.' );
	$user_id = (int) $created_user_id;

	update_site_option( $key, $network_value );
	update_option( $key, $site_value );
	update_user_meta( $user_id, $key, 'user-value' );
	$assert( 'user-value' === wp_get_user_preference( $user_id, $key ), 'User preference did not take precedence.' );

	update_user_meta( $user_id, $key, '' );
	$assert( '' === wp_get_user_preference( $user_id, $key ), 'An explicitly empty user preference incorrectly fell back.' );

	delete_user_meta( $user_id, $key );
	$assert( $site_value === wp_get_user_preference( $user_id, $key ), 'Site preference did not provide the first fallback.' );

	delete_option( $key );
	$assert( $network_value === wp_get_user_preference( $user_id, $key ), 'Network preference did not provide the final fallback.' );
} finally {
	if ( $user_id ) {
		delete_user_meta( $user_id, $key );
		wpmu_delete_user( $user_id );
	}
	delete_option( $key );
	delete_site_option( $key );
}

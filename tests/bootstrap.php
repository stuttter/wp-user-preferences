<?php

declare(strict_types=1);

define( 'ABSPATH', dirname( __DIR__ ) . '/' );

$GLOBALS['wpup_test'] = array();

function wpup_test_call( $name, $arguments ) {
	$GLOBALS['wpup_test']['calls'][ $name ][] = $arguments;

	if ( isset( $GLOBALS['wpup_test']['callbacks'][ $name ] ) ) {
		return $GLOBALS['wpup_test']['callbacks'][ $name ]( ...$arguments );
	}

	return $GLOBALS['wpup_test']['returns'][ $name ] ?? null;
}

function get_current_user_id() { return (int) ( wpup_test_call( __FUNCTION__, array() ) ?? 0 ); }
function metadata_exists( ...$arguments ) { return (bool) wpup_test_call( __FUNCTION__, $arguments ); }
function get_user_meta( ...$arguments ) { return wpup_test_call( __FUNCTION__, $arguments ); }
function get_option( ...$arguments ) { return wpup_test_call( __FUNCTION__, $arguments ); }
function is_multisite() { return (bool) wpup_test_call( __FUNCTION__, array() ); }
function get_site_option( ...$arguments ) { return wpup_test_call( __FUNCTION__, $arguments ); }
function apply_filters( $hook, $value, ...$arguments ) {
	$result = wpup_test_call( __FUNCTION__ . ':' . $hook, array_merge( array( $value ), $arguments ) );
	return null === $result ? $value : $result;
}

require_once dirname( __DIR__ ) . '/wp-user-preferences.php';

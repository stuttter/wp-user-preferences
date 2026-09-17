<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class UserPreferencesTest extends TestCase {
	protected function setUp(): void {
		$GLOBALS['wpup_test'] = array();
	}

	public function test_current_user_preference_uses_current_user_id(): void {
		$GLOBALS['wpup_test']['returns']['get_current_user_id'] = 17;
		$GLOBALS['wpup_test']['returns']['metadata_exists']     = true;
		$GLOBALS['wpup_test']['returns']['get_user_meta']       = 'personal';

		$this->assertSame( 'personal', wp_get_current_user_preference( 'timezone' ) );
		$this->assertSame( array( 'user', 17, 'timezone' ), $GLOBALS['wpup_test']['calls']['metadata_exists'][0] );
		$this->assertSame( array( 17, 'timezone', true ), $GLOBALS['wpup_test']['calls']['get_user_meta'][0] );
	}

	public function test_user_preference_takes_precedence_over_site_and_network(): void {
		$GLOBALS['wpup_test']['returns']['metadata_exists'] = true;
		$GLOBALS['wpup_test']['returns']['get_user_meta']   = 'personal';

		$this->assertSame( 'personal', wp_get_user_preference( 17, 'timezone' ) );
		$this->assertArrayNotHasKey( 'get_option', $GLOBALS['wpup_test']['calls'] ?? array() );
		$this->assertArrayNotHasKey( 'get_site_option', $GLOBALS['wpup_test']['calls'] ?? array() );
	}

	public function test_explicitly_empty_user_preference_does_not_fall_back(): void {
		$GLOBALS['wpup_test']['returns']['metadata_exists'] = true;
		$GLOBALS['wpup_test']['returns']['get_user_meta']   = '';
		$GLOBALS['wpup_test']['returns']['get_option']      = 'site';

		$this->assertSame( '', wp_get_user_preference( 17, 'timezone' ) );
		$this->assertArrayNotHasKey( 'get_option', $GLOBALS['wpup_test']['calls'] ?? array() );
	}

	public function test_explicitly_false_user_preference_does_not_fall_back(): void {
		$GLOBALS['wpup_test']['returns']['metadata_exists'] = true;
		$GLOBALS['wpup_test']['returns']['get_user_meta']   = false;
		$GLOBALS['wpup_test']['returns']['get_option']      = 'site';

		$this->assertFalse( wp_get_user_preference( 17, 'timezone' ) );
		$this->assertArrayNotHasKey( 'get_option', $GLOBALS['wpup_test']['calls'] ?? array() );
	}

	public function test_missing_user_preference_falls_back_to_site(): void {
		$GLOBALS['wpup_test']['returns']['metadata_exists'] = false;
		$GLOBALS['wpup_test']['returns']['get_option']      = 'site';

		$this->assertSame( 'site', wp_get_user_preference( 17, 'timezone' ) );
		$this->assertArrayNotHasKey( 'get_user_meta', $GLOBALS['wpup_test']['calls'] ?? array() );
	}

	public function test_missing_site_preference_falls_back_to_network_on_multisite(): void {
		$GLOBALS['wpup_test']['returns']['metadata_exists'] = false;
		$GLOBALS['wpup_test']['returns']['get_option']      = false;
		$GLOBALS['wpup_test']['returns']['is_multisite']    = true;
		$GLOBALS['wpup_test']['returns']['get_site_option'] = 'network';

		$this->assertSame( 'network', wp_get_user_preference( 17, 'timezone' ) );
		$this->assertSame( array( 'timezone' ), $GLOBALS['wpup_test']['calls']['get_site_option'][0] );
	}

	public function test_missing_site_preference_returns_false_on_single_site(): void {
		$GLOBALS['wpup_test']['returns']['metadata_exists'] = false;
		$GLOBALS['wpup_test']['returns']['get_option']      = false;
		$GLOBALS['wpup_test']['returns']['is_multisite']    = false;

		$this->assertFalse( wp_get_user_preference( 17, 'timezone' ) );
		$this->assertArrayNotHasKey( 'get_site_option', $GLOBALS['wpup_test']['calls'] ?? array() );
	}

	public function test_logged_out_request_still_uses_site_fallback(): void {
		$GLOBALS['wpup_test']['returns']['get_option'] = 'site';

		$this->assertSame( 'site', wp_get_user_preference( 0, 'timezone' ) );
		$this->assertArrayNotHasKey( 'metadata_exists', $GLOBALS['wpup_test']['calls'] ?? array() );
	}

	public function test_preference_key_map_can_be_filtered(): void {
		$GLOBALS['wpup_test']['callbacks']['apply_filters:wp_map_user_preference_key'] = static function () {
			return array(
				'user'    => 'personal_key',
				'site'    => 'site_key',
				'network' => 'network_key',
			);
		};
		$GLOBALS['wpup_test']['returns']['metadata_exists'] = false;
		$GLOBALS['wpup_test']['returns']['get_option']      = 'site';

		$this->assertSame( 'site', wp_get_user_preference( 17, 'preference' ) );
		$this->assertSame( array( 'site_key' ), $GLOBALS['wpup_test']['calls']['get_option'][0] );
	}

	public function test_resolved_preference_can_be_filtered(): void {
		$GLOBALS['wpup_test']['returns']['metadata_exists'] = false;
		$GLOBALS['wpup_test']['returns']['get_option']      = 'site';
		$GLOBALS['wpup_test']['callbacks']['apply_filters:wp_get_user_preference'] = static function ( $value, $user_id, $key ) {
			return $value . ':' . $user_id . ':' . $key;
		};

		$this->assertSame( 'site:17:timezone', wp_get_user_preference( 17, 'timezone' ) );
	}

	public function test_example_key_mapping_remains_compatible(): void {
		$this->assertSame(
			array( 'user' => 'one', 'site' => 'two', 'network' => 'three' ),
			wp_map_user_preference_key( 'some_other_preference' )
		);
	}
}

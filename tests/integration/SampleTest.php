<?php

namespace Acj\Tests\integration;

use WP_UnitTestCase;

class ConfigTest extends WP_UnitTestCase {
	public function test_user_exists() {
		$user = self::factory()->user->create_and_get();

		$this->assertTrue( $user->exists() );
	}
}

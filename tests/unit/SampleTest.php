<?php
/**
 * Sample test file.
 *
 * @package wp-community-connector
 * @since   0.1.0
 */

namespace Acj\Wpcc\Tests\unit;

/**
 * Functions test class.
 *
 * @since      1.0.0
 * @package    wp-community-connector
 * @author     achaitanyajami
 */
class SampleTest extends TestCase {
	/**
	 * Prepare the test environment before each test.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
	}

	public function testSample() {
		$this->assertEquals(1,1);
	}
}

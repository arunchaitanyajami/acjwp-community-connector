<?php
namespace Acj\Wpcc\Tests\unit;

use PHPUnit\Framework\TestCase as PhpUnitTestCase;

/**
 * Abstract base class for all unit test case implementations.
 */
abstract class TestCase extends PhpUnitTestCase{

	/**
	 * Prepare the test environment before each test.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
	}

	/**
	 * Clean up the test environment after each test.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		parent::tearDown();
	}
}

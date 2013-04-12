<?php namespace Orchestra\Html\Tests;

class ExpressionTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test constructing Orchestra\Html\Expression 
	 *
	 * @test
	 */
	public function testConstructMethod()
	{
		$expected = "foobar";
		$actual   = new \Orchestra\Html\Expression($expected);

		$this->assertInstanceOf('\Orchestra\Html\Expression', $actual);
		$this->assertEquals($expected, $actual);
		$this->assertEquals($expected, $actual->get());
	}
}
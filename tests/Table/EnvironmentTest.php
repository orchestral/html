<?php namespace Orchestra\Html\Tests\Table;

use Mockery as m;
use Orchestra\Html\Table\Environment;

class EnvironmentTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		m::close();
	}

	/**
	 * Test Orchestra\Html\Table\Environment::make() method.
	 *
	 * @test
	 */
	public function testMakeMethod()
	{
		$app = array(
			'config' => $config = m::mock('Config'),
		);
		
		$config->shouldReceive('get')->with('orchestra/html::table', array())->once()->andReturn(array());

		$stub   = new Environment($app);
		$output = $stub->make(function() {});

		$this->assertInstanceOf('\Orchestra\Html\Table\TableBuilder', $output);
	}

	/**
	 * Test Orchestra\Html\Table\Environment::of() method.
	 *
	 * @test
	 */
	public function testOfMethod()
	{
		$app = array(
			'config' => $config = m::mock('Config'),
		);
		
		$config->shouldReceive('get')->with('orchestra/html::table', array())->once()->andReturn(array());
		
		$stub   = new Environment($app);
		$output = $stub->of('foo', function() {});

		$this->assertInstanceOf('\Orchestra\Html\Table\TableBuilder', $output);
	}
}

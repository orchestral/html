<?php namespace Orchestra\Html\Tests\Form;

use Mockery as m;
use Orchestra\Html\Form\Environment;

class EnvironmentTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$app = m::mock('Application');
		$app->shouldReceive('instance')->andReturn(true);

		\Illuminate\Support\Facades\Config::setFacadeApplication($app);
	}

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
		$config = m::mock('Config');
		$config->shouldReceive('get')->with('orchestra/html::form', array())->once()->andReturn(array());

		\Illuminate\Support\Facades\Config::swap($config);

		$stub   = new Environment;
		$output = $stub->make(function() {});

		$this->assertInstanceOf('\Orchestra\Html\Form\FormBuilder', $output);
	}

	/**
	 * Test Orchestra\Html\Form\Environment::of() method.
	 *
	 * @test
	 */
	public function testOfMethod()
	{
		$config = m::mock('Config');
		$config->shouldReceive('get')->with('orchestra/html::form', array())->once()->andReturn(array());

		\Illuminate\Support\Facades\Config::swap($config);
		
		$stub   = new Environment;
		$output = $stub->of('foo', function() {});

		$this->assertInstanceOf('\Orchestra\Html\Form\FormBuilder', $output);
	}
}

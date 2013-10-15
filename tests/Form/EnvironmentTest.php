<?php namespace Orchestra\Html\Tests\Form;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Html\Form\Environment;

class EnvironmentTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Application instance.
	 *
	 * @var \Illuminate\Container\Container
	 */
	protected $app = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$this->app = new Container;
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->app);
		m::close();
	}

	/**
	 * Test Orchestra\Html\Table\Environment::make() method.
	 *
	 * @test
	 */
	public function testMakeMethod()
	{
		$app = $this->app;
		$app['config'] = $config = m::mock('Config');
		
		$config->shouldReceive('get')->once()
			->with('orchestra/html::form', array())->andReturn(array());

		$stub   = new Environment($app);
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
		$app = $this->app;
		$app['config'] = $config = m::mock('Config');
		
		$config->shouldReceive('get')->once()
			->with('orchestra/html::form', array())->andReturn(array());

		$stub   = new Environment($app);
		$output = $stub->of('foo', function() {});

		$this->assertInstanceOf('\Orchestra\Html\Form\FormBuilder', $output);
	}
}

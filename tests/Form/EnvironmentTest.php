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
		$app = array(
			'config' => $config = m::mock('Config'),
		);
		
		$config->shouldReceive('get')->with('orchestra/html::form', array())->once()->andReturn(array());

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
		$app = array(
			'config' => $config = m::mock('Config'),
		);
		
		$config->shouldReceive('get')->with('orchestra/html::form', array())->once()->andReturn(array());

		$stub   = new Environment($app);
		$output = $stub->of('foo', function() {});

		$this->assertInstanceOf('\Orchestra\Html\Form\FormBuilder', $output);
	}
}

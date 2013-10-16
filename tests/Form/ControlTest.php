<?php namespace Orchestra\Html\Tests\Form;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Html\Form\Control;

class ControlTest extends \PHPUnit_Framework_TestCase {

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
	 * Test Orchestra\Html\Form\Control configuration methods.
	 *
	 * @test
	 */
	public function testConfigMethods()
	{
		$config = array('foo' => 'foobar');
		$stub   = new Control($this->app, $config);

		$this->assertEquals($config, $stub->getConfig());
	}

	/**
	 * Test Orchestra\Html\Form\Control::render() throws exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testRenderMethodThrowsException()
	{
		$stub = new Control($this->app, array());

		$stub->render(
			array(),
			new \Illuminate\Support\Fluent(array('method' => 'foo'))
		);
	}
}

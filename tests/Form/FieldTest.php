<?php namespace Orchestra\Html\Tests\Form;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Html\Form\Field;

class FieldTest extends \PHPUnit_Framework_TestCase {

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
	 * Test Orchestra\Html\Form\Field::render() throws exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testRenderMethodThrowsException()
	{
		$stub = new Field($this->app, array());

		$stub->render(
			array(),
			new \Illuminate\Support\Fluent(array('method' => 'foo'))
		);
	}
}

<?php namespace Orchestra\Html\Tests\Table;

class EnvironmentTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$appMock = \Mockery::mock('Application')
			->shouldReceive('instance')
				->andReturn(true)
			->mock();

		\Illuminate\Support\Facades\Config::setFacadeApplication($appMock);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\Mockery::close();
	}

	/**
	 * Test Orchestra\Html\Table\Environment::make() method.
	 *
	 * @test
	 */
	public function testMakeMethod()
	{
		$configMock = \Mockery::mock('Config')
			->shouldReceive('get')
				->with('orchestra/html::table', array())
				->once()
				->andReturn(array());

		\Illuminate\Support\Facades\Config::swap($configMock->getMock());

		$stub   = new \Orchestra\Html\Table\Environment;
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
		$configMock = \Mockery::mock('Config')
			->shouldReceive('get')
				->with('orchestra/html::table', array())
				->once()
				->andReturn(array());

		\Illuminate\Support\Facades\Config::swap($configMock->getMock());
		
		$stub   = new \Orchestra\Html\Table\Environment;
		$output = $stub->of('foo', function() {});

		$this->assertInstanceOf('\Orchestra\Html\Table\TableBuilder', $output);
	}
}
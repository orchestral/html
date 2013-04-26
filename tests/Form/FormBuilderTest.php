<?php namespace Orchestra\Html\Tests\Form;

use Mockery as m;
use Orchestra\Html\Form\FormBuilder;

class FormBuilderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$app = m::mock('Application');
		$app->shouldReceive('instance')->andReturn(true);

		\Illuminate\Support\Facades\Config::setFacadeApplication($app);
		\Illuminate\Support\Facades\Lang::setFacadeApplication($app);
		\Illuminate\Support\Facades\View::setFacadeApplication($app);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		m::close();
	}
	
	/**
	 * Test construct a new Orchestra\Html\Form\FormBuilder.
	 *
	 * @test
	 */	
	public function testConstructMethod()
	{
		$config = m::mock('Config');
		$config->shouldReceive('get')->with('orchestra/html::form', array())->once()->andReturn(array());

		\Illuminate\Support\Facades\Config::swap($config);

		$stub = new FormBuilder(function () { });
		
		$refl = new \ReflectionObject($stub);
		$name = $refl->getProperty('name');
		$grid = $refl->getProperty('grid');
		
		$name->setAccessible(true);
		$grid->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Html\Form\FormBuilder', $stub);
		$this->assertInstanceOf('\Orchestra\Html\AbstractableBuilder', $stub);
		$this->assertInstanceOf('\Illuminate\Support\Contracts\RenderableInterface', $stub);
		
		$this->assertNull($name->getValue($stub));
		$this->assertNull($stub->name);
		$this->assertInstanceOf('\Orchestra\Html\Form\Grid', $grid->getValue($stub));
		$this->assertInstanceOf('\Orchestra\Html\Form\Grid', $stub->grid);
	}

	/**
	 * test Orchestra\Html\Form\FormBuilder::__get() throws an exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testMagicMethodThrowsException()
	{
		$stub = new FormBuilder(function () { });
		$stub->someInvalidRequest;
	}
	
	/**
	 * test Orchestra\Html\Form\FormBuilder::render() method.
	 *
	 * @test
	 */
	public function testRenderMethod()
	{
		$lang = m::mock('Lang');
		$view = m::mock('View');

		\Illuminate\Support\Facades\Lang::swap($lang);
		\Illuminate\Support\Facades\View::swap($view);

		$lang->shouldReceive('get')->twice()->andReturn(array());
		$view->shouldReceive('make')->twice()->andReturn($view)
			->shouldReceive('with')->twice()->andReturn($view)
			->shouldReceive('render')->twice()->andReturn('mocked');


		$data = new \Illuminate\Support\Fluent(array(
			'id'   => 1, 
			'name' => 'Laravel'
		));

		$mock1 = new FormBuilder(function ($form) use ($data)
		{
			$form->with($data);
			$form->attributes(array(
				'method' => 'POST',
				'url'    => 'http://localhost',
				'class'  => 'foo',
			));
		});

		$mock2 = new FormBuilder(function ($form) use ($data)
		{
			$form->with($data);
			$form->attributes = array(
				'method' => 'POST',
				'url'    => 'http://localhost',
				'class'  => 'foo'
			);
		});

		ob_start();
		echo $mock1;
		$output = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('mocked', $output);
		$this->assertEquals('mocked', $mock2->render());
	}
}

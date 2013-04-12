<?php namespace Orchestra\Html\Tests\Form;

class FormBuilderTest extends \PHPUnit_Framework_TestCase {

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
		\Illuminate\Support\Facades\Lang::setFacadeApplication($appMock);
		\Illuminate\Support\Facades\View::setFacadeApplication($appMock);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\Mockery::close();
	}
	
	/**
	 * Test construct a new Orchestra\Html\Form\FormBuilder.
	 *
	 * @test
	 */	
	public function testConstructMethod()
	{
		$configMock = \Mockery::mock('Config')
			->shouldReceive('get')
				->with('orchestra/html::form', array())
				->once()
				->andReturn(array());

		\Illuminate\Support\Facades\Config::swap($configMock->getMock());

		$stub = new \Orchestra\Html\Form\FormBuilder(function () { });
		
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
		$stub = new \Orchestra\Html\Form\FormBuilder(function () { });
		$stub->someInvalidRequest;
	}
	
	/**
	 * test Orchestra\Html\Form\FormBuilder::render() method.
	 *
	 * @test
	 */
	public function testRenderMethod()
	{
		$langMock = \Mockery::mock('Lang')
			->shouldReceive('get')
				->twice()
				->andReturn(array());

		\Illuminate\Support\Facades\Lang::swap($langMock->getMock());

		$viewMock = \Mockery::mock('View')
			->shouldReceive('make')
				->twice()
				->andReturn(\Mockery::self())
			->shouldReceive('with')
				->twice()
				->andReturn(\Mockery::self())
			->shouldReceive('render')
				->twice()
				->andReturn('mocked');

		\Illuminate\Support\Facades\View::swap($viewMock->getMock());

		$mock_data = new \Illuminate\Support\Fluent(array(
			'id' => 1, 
			'name' => 'Laravel'
		));

		$mock1 = new \Orchestra\Html\Form\FormBuilder(function ($form) use ($mock_data)
		{
			$form->row($mock_data);
			$form->attributes(array(
				'method' => 'POST',
				'action' => 'http://localhost',
				'class'  => 'foo',
			));
		});

		$mock2 = new \Orchestra\Html\Form\FormBuilder(function ($form) use ($mock_data)
		{
			$form->row($mock_data);
			$form->attributes = array(
				'method' => 'POST',
				'action' => 'http://localhost',
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

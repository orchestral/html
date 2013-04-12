<?php namespace Orchestra\Html\Tests\Table;

class TableBuilderTest extends \PHPUnit_Framework_TestCase {

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
		\Illuminate\Support\Facades\Request::setFacadeApplication($appMock);
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
	 * Test construct a new Orchestra\Html\Table\TableBuilder.
	 *
	 * @test
	 */	
	public function testConstructMethod()
	{
		$configMock = \Mockery::mock('Config')
			->shouldReceive('get')
				->with('orchestra/html::table', array())
				->once()
				->andReturn(array());

		\Illuminate\Support\Facades\Config::swap($configMock->getMock());

		$stub = new \Orchestra\Html\Table\TableBuilder(function () { });
		
		$refl = new \ReflectionObject($stub);
		$name = $refl->getProperty('name');
		$grid = $refl->getProperty('grid');
		
		$name->setAccessible(true);
		$grid->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Html\Table\TableBuilder', $stub);
		$this->assertInstanceOf('\Orchestra\Html\AbstractableBuilder', $stub);
		$this->assertInstanceOf('\Illuminate\Support\Contracts\RenderableInterface', $stub);
		
		$this->assertNull($name->getValue($stub));
		$this->assertNull($stub->name);
		$this->assertInstanceOf('\Orchestra\Html\Table\Grid', $grid->getValue($stub));
		$this->assertInstanceOf('\Orchestra\Html\Table\Grid', $stub->grid);
	}

	/**
	 * test Orchestra\Html\Table\TableBuilder::__get() throws an exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testMagicMethodThrowsException()
	{
		$stub = new \Orchestra\Html\Table\TableBuilder(function () { });
		$stub->someInvalidRequest;
	}
	
	/**
	 * test Orchestra\Html\Table\TableBuilder::render() method.
	 *
	 * @test
	 */
	public function testRenderMethod()
	{
		$requestMock = \Mockery::mock('Request')
			->shouldReceive('query')
				->twice()
				->andReturn(array());

		\Illuminate\Support\Facades\Request::swap($requestMock->getMock());

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

		$mock_data = array(
			new \Illuminate\Support\Fluent(array('id' => 1, 'name' => 'Laravel')),
			new \Illuminate\Support\Fluent(array('id' => 2, 'name' => 'Illuminate')),
			new \Illuminate\Support\Fluent(array('id' => 3, 'name' => 'Symfony')),
		);

		$mock1 = new \Orchestra\Html\Table\TableBuilder(function ($t) use ($mock_data)
		{
			$t->rows($mock_data);
			$t->attributes(array('class' => 'foo'));

			$t->column('id');
			$t->column(function ($c) 
			{
				$c->id = 'name';
				$c->label('Name');
				$c->value(function ($row)
				{
					return $row->name;
				});
			});
		});

		$mock2 = new \Orchestra\Html\Table\TableBuilder(function ($t) use ($mock_data)
		{
			$t->rows($mock_data);
			$t->attributes = array('class' => 'foo');

			$t->column('ID', 'id');
			$t->column('name', function ($c)
			{
				$c->value(function ($row)
				{
					return '<strong>'.$row->name.'</strong>';
				});
			});
		});

		ob_start();
		echo $mock1;
		$output = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('mocked', $output);
		$this->assertEquals('mocked', $mock2->render());
	}
}

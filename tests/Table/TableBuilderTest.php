<?php namespace Orchestra\Html\Tests\Table;

use Mockery as m;
use Orchestra\Html\Table\TableBuilder;

class TableBuilderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$app = m::mock('Application');
		$app->shouldReceive('instance')->andReturn(true);

		\Illuminate\Support\Facades\Config::setFacadeApplication($app);
		\Illuminate\Support\Facades\Lang::setFacadeApplication($app);
		\Illuminate\Support\Facades\Request::setFacadeApplication($app);
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
	 * Test construct a new Orchestra\Html\Table\TableBuilder.
	 *
	 * @test
	 */	
	public function testConstructMethod()
	{
		$config = m::mock('Config');
		$config->shouldReceive('get')->with('orchestra/html::table', array())->once()->andReturn(array());

		\Illuminate\Support\Facades\Config::swap($config);

		$stub = new TableBuilder(function () { });
		
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
		with(new TableBuilder(function () { }))->someInvalidRequest;
	}
	
	/**
	 * test Orchestra\Html\Table\TableBuilder::render() method.
	 *
	 * @test
	 */
	public function testRenderMethod()
	{
		$request = m::mock('Request');
		$lang    = m::mock('Lang');
		$view    = m::mock('View');

		\Illuminate\Support\Facades\Request::swap($request);
		\Illuminate\Support\Facades\Lang::swap($lang);
		\Illuminate\Support\Facades\View::swap($view);

		$request->shouldReceive('query')->twice()->andReturn(array());
		$lang->shouldReceive('get')->twice()->andReturn(array());


		$view->shouldReceive('make')->twice()->andReturn($view)
			->shouldReceive('with')->twice()->andReturn($view)
			->shouldReceive('render')->twice()->andReturn('mocked');

		$mock = array(
			new \Illuminate\Support\Fluent(array('id' => 1, 'name' => 'Laravel')),
			new \Illuminate\Support\Fluent(array('id' => 2, 'name' => 'Illuminate')),
			new \Illuminate\Support\Fluent(array('id' => 3, 'name' => 'Symfony')),
		);

		$mock1 = new TableBuilder(function ($t) use ($mock)
		{
			$t->rows($mock);
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

		$mock2 = new TableBuilder(function ($t) use ($mock)
		{
			$t->rows($mock);
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

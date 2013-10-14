<?php namespace Orchestra\Html\Tests\Table;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Html\Table\TableBuilder;

class TableBuilderTest extends \PHPUnit_Framework_TestCase {

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
	 * Test construct a new Orchestra\Html\Table\TableBuilder.
	 *
	 * @test
	 */	
	public function testConstructMethod()
	{
		$app = $this->app;
		$app['config'] = $config = m::mock('Config');

		$config->shouldReceive('get')->with('orchestra/html::table', array())->once()->andReturn(array());

		$stub = new TableBuilder($app, function () { });
		
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
		$app = $this->app;
		$app['config'] = $config = m::mock('Config');

		$config->shouldReceive('get')->with('orchestra/html::table', array())->once()->andReturn(array());

		with(new TableBuilder($app, function () { }))->someInvalidRequest;
	}
	
	/**
	 * test Orchestra\Html\Table\TableBuilder::render() method.
	 *
	 * @test
	 */
	public function testRenderMethod()
	{

		$app = $this->app;
		$app['config'] = $config = m::mock('Config');
		$app['request'] = $request = m::mock('Request');
		$app['translator'] = $lang = m::mock('Lang');
		$app['view'] = $view = m::mock('View');

		$config->shouldReceive('get')->with('orchestra/html::table', array())->twice()->andReturn(array());
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

		$mock1 = new TableBuilder($app, function ($t) use ($mock)
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

		$mock2 = new TableBuilder($app, function ($t) use ($mock)
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

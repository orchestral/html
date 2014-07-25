<?php namespace Orchestra\Html\Table\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Illuminate\Support\Fluent;
use Orchestra\Html\Table\Column;
use Orchestra\Html\Table\Grid;

class GridTest extends \PHPUnit_Framework_TestCase
{
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
     * Test instanceof Orchestra\Html\Table\Grid.
     *
     * @test
     */
    public function testInstanceOfGrid()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', array())->andReturn(array(
                'empty' => 'No data',
                'view'  => 'foo',
            ));

        $stub  = new Grid($app);
        $refl  = new \ReflectionObject($stub);
        $empty = $refl->getProperty('empty');
        $rows  = $refl->getProperty('rows');
        $view  = $refl->getProperty('view');

        $empty->setAccessible(true);
        $rows->setAccessible(true);
        $view->setAccessible(true);

        $this->assertInstanceOf('\Orchestra\Html\Table\Grid', $stub);
        $this->assertEquals('No data', $empty->getValue($stub));
        $this->assertEquals('foo', $view->getValue($stub));
        $this->assertEquals(array(), value($rows->getValue($stub)->attributes));
    }

    /**
     * Test Orchestra\Html\Table\Grid::with() method.
     *
     * @test
     */
    public function testWithMethod()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', array())->andReturn(array());

        $mock = array(new Fluent);
        $stub = new Grid($app);
        $stub->with($mock, false);

        $refl     = new \ReflectionObject($stub);
        $rows     = $refl->getProperty('rows');
        $model    = $refl->getProperty('model');
        $paginate = $refl->getProperty('paginate');

        $rows->setAccessible(true);
        $model->setAccessible(true);
        $paginate->setAccessible(true);

        $this->assertEquals($mock, $stub->rows());
        $this->assertEquals($mock, $model->getValue($stub));
        $this->assertFalse($paginate->getValue($stub));
        $this->assertTrue(isset($stub->model));
    }

    /**
     * Test Orchestra\Html\Table\Grid::with() method given a
     * Illuminate\Pagination\Paginator instance.
     *
     * @test
     */
    public function testWithMethodGivenPaginatorInstance()
    {
        $expected = array('foo');

        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', array())->andReturn(array());

        $model = m::mock('\Illuminate\Pagination\Paginator');
        $model->shouldReceive('getItems')->once()->andReturn($expected);

        $stub = new Grid($app);
        $stub->with($model);

        $this->assertEquals($expected, $stub->rows());
    }

    /**
     * Test Orchestra\Html\Table\Grid::with() method given a paginable
     * instance.
     *
     * @test
     */
    public function testWithMethodGivenModelBuilderInstance()
    {
        $expected = array('foo');

        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', array())->andReturn(array());

        $model = m::mock('\Illuminate\Database\Eloquent\Builder')->makePartial();
        $model->shouldReceive('paginate')->once()->andReturn($expected);

        $stub = new Grid($app);
        $stub->with($model);

        $this->assertEquals($expected, $stub->rows());
    }

    /**
     * Test Orchestra\Html\Table\Grid::with() method given a
     * Illuminate\Support\Contracts\ArrayableInterface instance.
     *
     * @test
     */
    public function testWithMethodGivenArrayableInterfaceInstance()
    {
        $expected = array('foo');

        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', array())->andReturn(array());

        $model = m::mock('\Illuminate\Support\Contracts\ArrayableInterface');
        $model->shouldReceive('toArray')->once()->andReturn($expected);

        $stub = new Grid($app);
        $stub->with($model);

        $this->assertEquals($expected, $stub->rows());
    }

    /**
     * Test Orchestra\Html\Table\Grid::with() method throws an exceptions
     * when $model can't be converted to array
     *
     * @expectedException \InvalidArgumentException
     */
    public function testWithMethodThrowsAnException()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', array())->andReturn(array());

        $model = 'Foo';

        $stub = new Grid($app);
        $stub->with($model, false);

        $stub->rows();
    }

    /**
     * Test Orchestra\Html\Table\Grid::layout() method.
     *
     * @test
     */
    public function testLayoutMethod()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', array())->andReturn(array());

        $stub = new Grid($app);

        $refl = new \ReflectionObject($stub);
        $view = $refl->getProperty('view');
        $view->setAccessible(true);

        $stub->layout('horizontal');
        $this->assertEquals('orchestra/html::table.horizontal', $view->getValue($stub));

        $stub->layout('vertical');
        $this->assertEquals('orchestra/html::table.vertical', $view->getValue($stub));

        $stub->layout('foo');
        $this->assertEquals('foo', $view->getValue($stub));
    }

    /**
     * Test Orchestra\Html\Table\Grid::of() method.
     *
     * @test
     */
    public function testOfMethod()
    {
        $me  = $this;
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', array())->andReturn(array());

        $stub = new Grid($app);
        $stub->with(array(
            new Fluent(array('foo1' => 'Foo1')),
        ), false);

        $expected = array(
            new Column(array(
                'id'         => 'id',
                'label'      => 'Id',
                'value'      => function ($row) {
                    return $row->id;
                },
                'headers'    => array(),
                'attributes' => function ($row) {
                    return array();
                }
            )),
            new Column(array(
                'id'         => 'foo1',
                'label'      => 'Foo1',
                'value'      => 'Foo1 value',
                'headers'    => array(),
                'attributes' => function ($row) {
                    return array();
                }
            )),
            new Column(array(
                'id'         => 'foo2',
                'label'      => 'Foo2',
                'value'      => 'Foo2 value',
                'headers'    => array(),
                'attributes' => function ($row) {
                    return array();
                }
            ))
        );

        $stub->column('id');

        $stub->column(function ($c) {
            $c->id('foo1')->label('Foo1');
            $c->value('Foo1 value');
        });

        $stub->column('Foo2', 'foo2')->value('Foo2 value');

        $stub->attributes = array('class' => 'foo');

        $output = $stub->of('id', function ($fluent) use ($me) {
            $me->assertInstanceOf('\Illuminate\Support\Fluent', $fluent);
        });

        $this->assertEquals('Id', $output->label);
        $this->assertEquals('id', $output->id);
        $this->assertEquals(array(), call_user_func($output->attributes, new Fluent));
        $this->assertEquals(5, call_user_func($output->value, new Fluent(array('id' => 5))));

        $this->assertEquals(array('class' => 'foo'), $stub->attributes);
        $this->assertEquals($expected, $stub->columns());
    }

    /**
     * Test Orchestra\Html\Table\Grid::of() method throws exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testOfMethodThrowsException()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', array())->andReturn(array());

        $stub = new Grid($app);
        $stub->of('id');
    }

    /**
     * Test Orchestra\Html\Table\Grid::attributes() method.
     *
     * @test
     */
    public function testAttributesMethod()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', array())->andReturn(array());

        $stub = new Grid($app);

        $refl       = new \ReflectionObject($stub);
        $attributes = $refl->getProperty('attributes');
        $attributes->setAccessible(true);

        $stub->attributes(array('class' => 'foo'));

        $this->assertEquals(array('class' => 'foo'), $attributes->getValue($stub));
        $this->assertEquals(array('class' => 'foo'), $stub->attributes());

        $stub->attributes('id', 'foobar');

        $this->assertEquals(array('id' => 'foobar', 'class' => 'foo'), $attributes->getValue($stub));
        $this->assertEquals(array('id' => 'foobar', 'class' => 'foo'), $stub->attributes());
    }

    /**
     * Test Orchestra\Html\Table\Grid magic method __call() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodCallThrowsException()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', array())->andReturn(array());

        $stub = new Grid($app);
        $stub->invalidMethod();
    }

    /**
     * Test Orchestra\Html\Table\Grid magic method __get() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodGetThrowsException()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', array())->andReturn(array());

        $stub = new Grid($app);
        $stub->invalidProperty;
    }

    /**
     * Test Orchestra\Html\Table\Grid magic method __set() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodSetThrowsException()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', array())->andReturn(array());

        $stub = new Grid($app);
        $stub->invalidProperty = array('foo');
    }

    /**
     * Test Orchestra\Html\Table\Grid magic method __set() throws
     * exception when $values is not an array.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodSetThrowsExceptionValuesNotAnArray()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', array())->andReturn(array());

        $stub = new Grid($app);
        $stub->attributes = 'foo';
    }

    /**
     * Test Orchestra\Html\Table\Grid magic method __isset() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodIssetThrowsException()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', array())->andReturn(array());

        $stub = new Grid($app);
        isset($stub->invalidProperty) ? true : false;
    }
}

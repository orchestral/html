<?php namespace Orchestra\Html\Table\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Illuminate\Support\Fluent;
use Orchestra\Html\Table\Column;
use Orchestra\Html\Table\Grid;

class GridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test instanceof Orchestra\Html\Table\Grid.
     *
     * @test
     */
    public function testInstanceOfGrid()
    {
        $app = new Container;
        $app['config'] = $config = m::mock('\Illuminate\Contracts\Config\Config');

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
        $stub = new Grid($this->getContainer());

        $mock = array(new Fluent);
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
        $stub = new Grid($this->getContainer());

        $model = m::mock('\Illuminate\Pagination\Paginator');
        $model->shouldReceive('items')->once()->andReturn($expected);

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
        $stub = new Grid($this->getContainer());

        $model = m::mock('\Illuminate\Database\Eloquent\Builder')->makePartial();
        $model->shouldReceive('paginate')->once()->andReturn($expected);

        $stub->with($model);

        $this->assertEquals($expected, $stub->rows());
    }

    /**
     * Test Orchestra\Html\Table\Grid::with() method given a
     * Illuminate\Contracts\Support\Arrayable instance.
     *
     * @test
     */
    public function testWithMethodGivenArrayableInterfaceInstance()
    {
        $expected = array('foo');
        $stub = new Grid($this->getContainer());

        $model = m::mock('\Illuminate\Contracts\Support\Arrayable');
        $model->shouldReceive('toArray')->once()->andReturn($expected);

        $stub->with($model);

        $this->assertEquals($expected, $stub->rows());
    }

    /**
     * Test Orchestra\Html\Table\Grid::with() method given a
     * Query Builder instance when paginated.
     *
     * @test
     */
    public function testWithMethodGivenQueryBuilderInstanceWhenPaginated()
    {
        $expected = array('foo');
        $stub = new Grid($this->getContainer());

        $model = m::mock('\Illuminate\Database\Query\Builder');
        $arrayable = m::mock('\Illuminate\Contracts\Support\Arrayable');

        $model->shouldReceive('paginate')->once()->with(25)->andReturn($arrayable);
        $arrayable->shouldReceive('toArray')->once()->andReturn($expected);

        $stub->paginate(25);
        $stub->with($model);

        $this->assertEquals($expected, $stub->rows());
    }

    /**
     * Test Orchestra\Html\Table\Grid::with() method given a
     * Query Builder instance when not paginated.
     *
     * @test
     */
    public function testWithMethodGivenQueryBuilderInstanceWhenNotPaginated()
    {
        $expected = array('foo');
        $stub = new Grid($this->getContainer());

        $model = m::mock('\Illuminate\Database\Query\Builder');
        $arrayable = m::mock('\Illuminate\Contracts\Support\Arrayable');

        $model->shouldReceive('get')->once()->andReturn($arrayable);
        $arrayable->shouldReceive('toArray')->once()->andReturn($expected);

        $stub->with($model);
        $stub->paginate(null);

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
        $stub = new Grid($this->getContainer());

        $model = 'Foo';

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
        $stub = new Grid($this->getContainer());

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
        $me   = $this;
        $stub = new Grid($this->getContainer());

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
        $stub = new Grid($this->getContainer());
        $stub->of('id');
    }

    /**
     * Test Orchestra\Html\Table\Grid::paginate() method.
     *
     * @test
     */
    public function testPaginateMethod()
    {
        $stub = new Grid($this->getContainer());
        $refl = new \ReflectionObject($stub);

        $perPage  = $refl->getProperty('perPage');
        $paginate = $refl->getProperty('paginate');

        $perPage->setAccessible(true);
        $paginate->setAccessible(true);

        $this->assertNull($perPage->getValue($stub));
        $this->assertFalse($paginate->getValue($stub));

        $stub->paginate(25);

        $this->assertEquals(25, $perPage->getValue($stub));
        $this->assertTrue($paginate->getValue($stub));

        $stub->paginate(2.5);

        $this->assertNull($perPage->getValue($stub));
        $this->assertFalse($paginate->getValue($stub));

        $stub->paginate(-10);

        $this->assertNull($perPage->getValue($stub));
        $this->assertFalse($paginate->getValue($stub));

        $stub->paginate(true);

        $this->assertNull($perPage->getValue($stub));
        $this->assertTrue($paginate->getValue($stub));

        $stub->paginate(false);

        $this->assertNull($perPage->getValue($stub));
        $this->assertFalse($paginate->getValue($stub));
    }

    /**
     * Test Orchestra\Html\Table\Grid::searchable() method.
     *
     * @test
     */
    public function testSearchableMethod()
    {
        $attributes = array('email', 'fullname');
        $app = $this->getContainer();

        $app['request'] = $request = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('input')->once()->with('q')->andReturn('orchestra*');

        $stub = m::mock('\Orchestra\Html\Table\Grid[setupWildcardQueryFilter]', array($app))
                    ->shouldAllowMockingProtectedMethods();

        $model = m::mock('\Illuminate\Database\Query\Builder');

        $stub->shouldReceive('setupWildcardQueryFilter')->once()->with($model, 'orchestra*', $attributes)->andReturnNull();

        $stub->with($model);

        $this->assertNull($stub->searchable($attributes));

        $this->assertEquals($attributes, $stub->get('search.attributes'));
        $this->assertEquals('q', $stub->get('search.key'));
        $this->assertEquals('orchestra*', $stub->get('search.value'));
    }

    /**
     * Test Orchestra\Html\Table\Grid::searchable() method
     * throws exception when model is not a query builder.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testSearchableMethodThrowsException()
    {
        $attributes = array('email', 'fullname');

        $stub = new Grid($this->getContainer());

        $stub->with('Foo');

        $stub->searchable($attributes);
    }

    /**
     * Test Orchestra\Html\Table\Grid::sortable() method.
     *
     * @test
     */
    public function testSortableMethod()
    {
        $app = $this->getContainer();
        $app['request'] = $request = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('input')->once()->with('order_by')->andReturn('email')
            ->shouldReceive('input')->once()->with('direction')->andReturn('desc');

        $stub = m::mock('\Orchestra\Html\Table\Grid[setupBasicQueryFilter]', array($app))
            ->shouldAllowMockingProtectedMethods();

        $model = m::mock('\Illuminate\Database\Query\Builder');

        $stub->shouldReceive('setupBasicQueryFilter')->once()
            ->with($model, array('order_by' => 'email', 'direction' => 'desc'))->andReturnNull();

        $stub->with($model);

        $this->assertNull($stub->sortable());

        $this->assertEquals(array('key' => 'order_by', 'value' => 'email'), $stub->get('filter.order_by'));
        $this->assertEquals(array('key' => 'direction', 'value' => 'desc'), $stub->get('filter.direction'));
    }

    /**
     * Test Orchestra\Html\Table\Grid::sortable() method
     * throws exception when model is not a query builder.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testSortableMethodThrowsException()
    {
        $attributes = array('email', 'fullname');

        $stub = new Grid($this->getContainer());

        $stub->with('Foo');

        $stub->sortable($attributes);
    }

    /**
     * Test Orchestra\Html\Table\Grid::attributes() method.
     *
     * @test
     */
    public function testAttributesMethod()
    {
        $stub = new Grid($this->getContainer());

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
        $stub = new Grid($this->getContainer());

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
        $stub = new Grid($this->getContainer());

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
        $stub = new Grid($this->getContainer());

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
        $stub = new Grid($this->getContainer());

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
        $stub = new Grid($this->getContainer());

        isset($stub->invalidProperty) ? true : false;
    }

    /**
     * Get app container.
     *
     * @return Container
     */
    protected function getContainer()
    {
        $app = new Container;
        $app['config'] = $config = m::mock('\Illuminate\Contracts\Config\Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', array())->andReturn(array());

        return $app;
    }
}
